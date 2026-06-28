import os
from datetime import date, timedelta
from urllib.parse import urljoin, urlparse
from uuid import uuid4

from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select, WebDriverWait


WAIT_SECONDS = 30


def env(name, default):
    return os.environ.get(name, default)


ADMIN_EMAIL = env("SELENIUM_ADMIN_EMAIL", "admin.selenium@example.test")
ADMIN_PASSWORD = env("SELENIUM_ADMIN_PASSWORD", "AdminPass123!")


def absolute_url(base_url, path):
    return urljoin(base_url + "/", path.lstrip("/"))


def wait_for_path(driver, expected_path):
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: urlparse(current.current_url).path == expected_path
    )


def set_field_value(driver, selector, value):
    field = driver.find_element(By.CSS_SELECTOR, selector)
    driver.execute_script(
        """
        arguments[0].value = arguments[1];
        arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
        arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        """,
        field,
        value,
    )


def click_when_ready(driver, locator):
    element = WebDriverWait(driver, WAIT_SECONDS).until(EC.element_to_be_clickable(locator))
    driver.execute_script("arguments[0].scrollIntoView({ block: 'center' });", element)
    element.click()
    return element


def click_element(driver, element):
    driver.execute_script("arguments[0].scrollIntoView({ block: 'center' });", element)
    element.click()


def submit_login(driver, base_url):
    driver.get(absolute_url(base_url, "/membres.php?lang=fr"))

    email_input = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.ID, "email"))
    )
    password_input = driver.find_element(By.ID, "password")

    email_input.clear()
    email_input.send_keys(ADMIN_EMAIL)
    password_input.clear()
    password_input.send_keys(ADMIN_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

    wait_for_path(driver, "/manager/dashboard.php")


def wait_for_admin_calendar(driver):
    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.presence_of_element_located((By.ID, "admin-calendar"))
    )
    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, "#adminCalendar .fc"))
    )


def wait_for_calendar_text(driver, text):
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: text in current.find_element(By.ID, "adminCalendar").text
    )


def open_new_calendar_event(driver):
    click_when_ready(driver, (By.ID, "btnNewEvent"))
    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, "#eventDialog[open]"))
    )


def test_admin_can_manage_single_and_recurring_calendar_events(driver, base_url):
    submit_login(driver, base_url)
    wait_for_admin_calendar(driver)

    unique = uuid4().hex[:8]
    today = date.today()
    single_title = f"Selenium ponctuel enfants {unique}"
    recurring_title = f"Selenium repete ados {unique}"
    day_value = str((today.weekday() + 1) % 7)

    open_new_calendar_event(driver)
    Select(driver.find_element(By.ID, "eventAudience")).select_by_value("children")
    Select(driver.find_element(By.ID, "eventType")).select_by_value("single")
    set_field_value(driver, "#eventTitle", single_title)
    set_field_value(driver, "#eventStart", f"{today.isoformat()}T17:00")
    set_field_value(driver, "#eventEnd", f"{today.isoformat()}T18:00")
    click_when_ready(driver, (By.ID, "btnSaveEvent"))

    wait_for_path(driver, "/manager/dashboard.php")
    wait_for_admin_calendar(driver)
    wait_for_calendar_text(driver, single_title)

    open_new_calendar_event(driver)
    Select(driver.find_element(By.ID, "eventAudience")).select_by_value("teens")
    Select(driver.find_element(By.ID, "eventType")).select_by_value("recurring")
    set_field_value(driver, "#eventTitle", recurring_title)
    set_field_value(driver, "#eventStartTime", "18:00")
    set_field_value(driver, "#eventEndTime", "19:00")
    set_field_value(driver, "#eventStartRecur", today.isoformat())
    set_field_value(driver, "#eventEndRecur", (today + timedelta(days=7)).isoformat())
    driver.find_element(By.CSS_SELECTOR, f"input[data-day-checkbox][value='{day_value}']").click()
    click_when_ready(driver, (By.ID, "btnSaveEvent"))

    wait_for_path(driver, "/manager/dashboard.php")
    wait_for_admin_calendar(driver)
    wait_for_calendar_text(driver, recurring_title)

    click_when_ready(driver, (By.CSS_SELECTOR, ".calendar-filter[data-filter='teens']"))
    wait_for_calendar_text(driver, recurring_title)

    click_when_ready(driver, (By.CSS_SELECTOR, ".calendar-filter[data-filter='children']"))
    wait_for_calendar_text(driver, single_title)
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: recurring_title not in current.find_element(By.ID, "adminCalendar").text
    )

    click_when_ready(driver, (By.CSS_SELECTOR, ".calendar-filter[data-filter='club']"))
    recurring_row = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.presence_of_element_located(
            (By.XPATH, f"//tr[@data-calendar-row][.//*[contains(normalize-space(), '{recurring_title}')]]")
        )
    )
    click_element(driver, recurring_row.find_element(By.XPATH, ".//button[normalize-space()='Dupliquer']"))

    wait_for_path(driver, "/manager/dashboard.php")
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: f"{recurring_title} (copie)" in current.find_element(By.TAG_NAME, "body").text
    )
    copied_row = driver.find_element(
        By.XPATH,
        f"//tr[@data-calendar-row][.//*[contains(normalize-space(), '{recurring_title} (copie)')]]",
    )
    assert "Brouillon" in copied_row.text

    click_element(copied_row.find_element(By.CSS_SELECTOR, "input[data-calendar-select]"))
    Select(driver.find_element(By.ID, "calendarBulkAction")).select_by_value("publish")
    click_when_ready(driver, (By.CSS_SELECTOR, "#calendarBulkForm button[type='submit']"))

    wait_for_path(driver, "/manager/dashboard.php")
    copied_row = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.presence_of_element_located(
            (By.XPATH, f"//tr[@data-calendar-row][.//*[contains(normalize-space(), '{recurring_title} (copie)')]]")
        )
    )
    assert "Publie" in copied_row.text


def test_admin_can_create_meal_reservation_from_dashboard(driver, base_url):
    submit_login(driver, base_url)

    name = f"Reservation admin Selenium {uuid4().hex[:8]}"
    email = "admin-reservation.selenium@example.test"

    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.ID, "admin_profile_name"))
    )
    set_field_value(driver, "#admin_profile_name", name)
    set_field_value(driver, "#admin_contact_email", email)
    set_field_value(driver, "#admin_contact_phone", "+32 499 00 00 00")
    set_field_value(driver, "#admin_repas_adulte", "1")
    set_field_value(driver, "#admin_repas_enfant", "2")
    set_field_value(driver, "#admin_meal_notes", "Saisie depuis Selenium admin")
    click_when_ready(driver, (By.CSS_SELECTOR, "#admin-meal button[type='submit']"))

    wait_for_path(driver, "/manager/dashboard.php")
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: name in current.find_element(By.TAG_NAME, "body").text
    )

    body = driver.find_element(By.TAG_NAME, "body").text
    assert email in body
    assert "admin_public" in body

    row = driver.find_element(
        By.XPATH,
        f"//section[@id='admin-meal-summary']//tr[.//*[contains(normalize-space(), '{name}')]]",
    )
    status = Select(row.find_element(By.CSS_SELECTOR, "select[name='status']"))
    assert status.first_selected_option.get_attribute("value") == "confirmed"

    status.select_by_value("paid")
    click_element(driver, row.find_element(By.XPATH, ".//button[normalize-space()='OK']"))

    wait_for_path(driver, "/manager/dashboard.php")
    row = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.presence_of_element_located(
            (
                By.XPATH,
                f"//section[@id='admin-meal-summary']//tr[.//*[contains(normalize-space(), '{name}')]]",
            )
        )
    )
    assert Select(row.find_element(By.CSS_SELECTOR, "select[name='status']")).first_selected_option.get_attribute("value") == "paid"
