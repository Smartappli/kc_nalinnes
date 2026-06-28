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
    set_element_value(driver, field, value)


def set_element_value(driver, field, value):
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


def submit_form(driver, form):
    click_element(driver, form.find_element(By.CSS_SELECTOR, "button"))


def logout(driver):
    click_when_ready(
        driver,
        (By.XPATH, "//form[.//input[@name='action' and @value='logout']]//button"),
    )


def wait_for_member_row(driver, email):
    return WebDriverWait(driver, WAIT_SECONDS).until(
        EC.presence_of_element_located(
            (By.XPATH, f"//tr[@data-member-row][.//*[contains(normalize-space(), '{email}')]]")
        )
    )


def wait_for_member_row_text(driver, email, text):
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: text
        in current.find_element(
            By.XPATH,
            f"//tr[@data-member-row][.//*[contains(normalize-space(), '{email}')]]",
        ).text
    )
    return wait_for_member_row(driver, email)


def submit_login_as(driver, base_url, email, password):
    driver.get(absolute_url(base_url, "/membres.php?lang=fr"))

    email_input = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.ID, "email"))
    )
    password_input = driver.find_element(By.ID, "password")

    email_input.clear()
    email_input.send_keys(email)
    password_input.clear()
    password_input.send_keys(password)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def submit_login(driver, base_url):
    submit_login_as(driver, base_url, ADMIN_EMAIL, ADMIN_PASSWORD)

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


def test_admin_can_create_update_member_dependents_and_reset_password(driver, base_url):
    submit_login(driver, base_url)

    unique = uuid4().hex[:8]
    initial_email = f"member-{unique}@selenium.example.test"
    updated_email = f"member-{unique}-updated@selenium.example.test"
    initial_password = "MemberPass123!"
    reset_password = "ResetPass123!"
    member_name = f"Membre Selenium {unique}"
    updated_name = f"Membre Selenium maj {unique}"
    first_name = f"Prenom {unique}"
    last_name = f"Nom {unique}"
    updated_first_name = f"PrenomMaj {unique}"
    updated_last_name = f"NomMaj {unique}"
    child_name = f"Enfant Selenium {unique}"
    updated_child_name = f"Profil Adulte Selenium {unique}"
    dated_grade = f"9e kyu {unique}"
    grade_date = "2026-05-01"
    payment_year = date.today().year
    paid_at = f"{payment_year}-01-15"

    create_form = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located(
            (By.XPATH, "//section[@id='admin-users']//form[.//input[@name='action' and @value='member_create']]")
        )
    )
    set_element_value(driver, create_form.find_element(By.CSS_SELECTOR, "input[name='new_member_email']"), initial_email)
    set_element_value(driver, create_form.find_element(By.CSS_SELECTOR, "input[name='new_member_first_name']"), first_name)
    set_element_value(driver, create_form.find_element(By.CSS_SELECTOR, "input[name='new_member_last_name']"), last_name)
    set_element_value(driver, create_form.find_element(By.CSS_SELECTOR, "input[name='new_member_username']"), member_name)
    set_element_value(driver, create_form.find_element(By.CSS_SELECTOR, "input[name='new_member_password']"), initial_password)
    set_element_value(driver, create_form.find_element(By.CSS_SELECTOR, "input[name='new_member_grade']"), "10e kyu")
    Select(create_form.find_element(By.CSS_SELECTOR, "select[name='new_member_role']")).select_by_value("member")
    submit_form(driver, create_form)

    wait_for_path(driver, "/manager/dashboard.php")
    member_row = wait_for_member_row(driver, initial_email)
    assert first_name in member_row.text
    assert last_name in member_row.text
    assert member_name in member_row.text
    assert "10e kyu" in member_row.text

    profile_form = member_row.find_element(
        By.XPATH,
        ".//form[.//input[@name='action' and @value='member_profile_update']]",
    )
    set_element_value(driver, profile_form.find_element(By.CSS_SELECTOR, "input[name='target_email']"), updated_email)
    set_element_value(driver, profile_form.find_element(By.CSS_SELECTOR, "input[name='target_username']"), updated_name)
    set_element_value(driver, profile_form.find_element(By.CSS_SELECTOR, "input[name='target_first_name']"), updated_first_name)
    set_element_value(driver, profile_form.find_element(By.CSS_SELECTOR, "input[name='target_last_name']"), updated_last_name)
    submit_form(driver, profile_form)

    wait_for_path(driver, "/manager/dashboard.php")
    member_row = wait_for_member_row(driver, updated_email)
    assert updated_first_name in member_row.text
    assert updated_last_name in member_row.text
    assert updated_name in member_row.text

    grade_form = member_row.find_element(
        By.XPATH,
        ".//form[.//input[@name='action' and @value='member_grade_add']]",
    )
    set_element_value(driver, grade_form.find_element(By.CSS_SELECTOR, "input[name='grade']"), dated_grade)
    set_element_value(driver, grade_form.find_element(By.CSS_SELECTOR, "input[name='obtained_at']"), grade_date)
    submit_form(driver, grade_form)

    wait_for_path(driver, "/manager/dashboard.php")
    member_row = wait_for_member_row_text(driver, updated_email, f"{dated_grade} - {grade_date}")

    annual_payment_form = member_row.find_element(
        By.XPATH,
        ".//form[.//input[@name='action' and @value='member_payment_update'] and .//input[@name='period_type' and @value='annual']]",
    )
    Select(annual_payment_form.find_element(By.CSS_SELECTOR, "select[name='payment_status']")).select_by_value("paid")
    set_element_value(driver, annual_payment_form.find_element(By.CSS_SELECTOR, "input[name='paid_at']"), paid_at)
    submit_form(driver, annual_payment_form)

    wait_for_path(driver, "/manager/dashboard.php")
    assert f"payment_year={payment_year}" in driver.current_url
    member_row = wait_for_member_row_text(driver, updated_email, f"Annee {payment_year}: Paye")
    assert "Mutuelle" in member_row.text

    add_dependent_form = member_row.find_element(
        By.XPATH,
        ".//form[.//input[@name='action' and @value='member_dependent_add']]",
    )
    set_element_value(driver, add_dependent_form.find_element(By.CSS_SELECTOR, "input[name='dependent_name']"), child_name)
    set_element_value(driver, add_dependent_form.find_element(By.CSS_SELECTOR, "input[name='dependent_birthdate']"), "2016-04-12")
    Select(add_dependent_form.find_element(By.CSS_SELECTOR, "select[name='dependent_is_minor']")).select_by_value("1")
    submit_form(driver, add_dependent_form)

    wait_for_path(driver, "/manager/dashboard.php")
    member_row = wait_for_member_row_text(driver, updated_email, child_name)
    assert "mineur" in member_row.text

    dependent_form = member_row.find_element(
        By.XPATH,
        f".//form[.//input[@name='action' and @value='member_dependent_update'] and .//input[@name='dependent_name' and @value='{child_name}']]",
    )
    set_element_value(driver, dependent_form.find_element(By.CSS_SELECTOR, "input[name='dependent_name']"), updated_child_name)
    set_element_value(driver, dependent_form.find_element(By.CSS_SELECTOR, "input[name='dependent_birthdate']"), "")
    Select(dependent_form.find_element(By.CSS_SELECTOR, "select[name='dependent_is_minor']")).select_by_value("0")
    submit_form(driver, dependent_form)

    wait_for_path(driver, "/manager/dashboard.php")
    member_row = wait_for_member_row_text(driver, updated_email, updated_child_name)
    assert "adulte" in member_row.text
    dependent_mutuelle_link = member_row.find_element(
        By.XPATH,
        f".//a[contains(@href, 'download=member_mutuelle') and contains(@href, 'dependent_id=') and contains(normalize-space(), '{updated_child_name}')]",
    )
    assert f"year={payment_year}" in dependent_mutuelle_link.get_attribute("href")

    password_form = member_row.find_element(
        By.XPATH,
        ".//form[.//input[@name='action' and @value='member_password_reset']]",
    )
    set_element_value(driver, password_form.find_element(By.CSS_SELECTOR, "input[name='new_member_password']"), reset_password)
    submit_form(driver, password_form)

    wait_for_path(driver, "/manager/dashboard.php")
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: "Mot de passe membre mis a jour" in current.find_element(By.TAG_NAME, "body").text
    )

    logout(driver)
    wait_for_path(driver, "/membres.php")
    submit_login_as(driver, base_url, updated_email, reset_password)
    wait_for_path(driver, "/member/dashboard.php")
    body = driver.find_element(By.TAG_NAME, "body").text
    assert updated_email in body
    assert updated_first_name in body
    assert updated_last_name in body
    assert updated_child_name in body
    assert "La cotisation annuelle" not in body
    assert "mutualia-ac-sport-fr.pdf" in body


def test_admin_can_update_meal_settings_and_public_page_reflects_them(driver, base_url):
    submit_login(driver, base_url)

    unique = uuid4().hex[:8]
    adult_menu = f"Menu adulte Selenium {unique}"
    child_menu = f"Menu enfant Selenium {unique}"
    meal_day = date.today() + timedelta(days=45)
    deadline_day = date.today() + timedelta(days=30)
    meal_at = f"{meal_day.isoformat()}T19:30"
    deadline_at = f"{deadline_day.isoformat()}T12:15"
    meal_label = f"{meal_day.day:02d}/{meal_day.month:02d}/{meal_day.year} 19:30"
    deadline_label = f"{deadline_day.day:02d}/{deadline_day.month:02d}/{deadline_day.year} 12:15"

    settings_form = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located(
            (By.XPATH, "//section[@id='admin-meal']//form[.//input[@name='action' and @value='meal_settings_update']]")
        )
    )
    set_element_value(driver, settings_form.find_element(By.CSS_SELECTOR, "textarea[name='adult_menu']"), adult_menu)
    set_element_value(driver, settings_form.find_element(By.CSS_SELECTOR, "textarea[name='child_menu']"), child_menu)
    set_element_value(driver, settings_form.find_element(By.CSS_SELECTOR, "input[name='adult_price']"), "23.50")
    set_element_value(driver, settings_form.find_element(By.CSS_SELECTOR, "input[name='child_price']"), "12.25")
    set_element_value(driver, settings_form.find_element(By.CSS_SELECTOR, "input[name='meal_at']"), meal_at)
    set_element_value(driver, settings_form.find_element(By.CSS_SELECTOR, "input[name='reservation_deadline_at']"), deadline_at)
    submit_form(driver, settings_form)

    wait_for_path(driver, "/manager/dashboard.php")
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: "Parametres du repas enregistres" in current.find_element(By.TAG_NAME, "body").text
    )

    driver.get(absolute_url(base_url, "/reservation-repas.php?lang=fr"))
    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.ID, "repas_adulte"))
    )
    body = driver.find_element(By.TAG_NAME, "body").text

    assert adult_menu in body
    assert child_menu in body
    assert "23,50 EUR" in body
    assert "12,25 EUR" in body
    assert meal_label in body
    assert deadline_label in body
    assert "Reservations cloturees" not in body


def test_admin_can_create_meal_reservation_from_dashboard(driver, base_url):
    submit_login(driver, base_url)

    name = f"Reservation admin Selenium {uuid4().hex[:8]}"
    email = "admin-reservation.selenium@example.test"

    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.ID, "admin_profile_name"))
    )
    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.ID, "meal_adult_menu"))
    )
    WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.CSS_SELECTOR, "#admin-users input[name='target_email']"))
    )
    assert driver.find_element(By.ID, "new_member_email").is_displayed()
    assert driver.find_element(By.ID, "new_member_password").is_displayed()
    assert driver.find_element(By.CSS_SELECTOR, "#admin-users select[name='new_member_role']").is_displayed()
    assert driver.find_element(By.CSS_SELECTOR, "#admin-users input[name='target_username']").is_displayed()
    assert driver.find_element(By.ID, "memberPaymentYear").is_displayed()
    assert driver.find_element(By.CSS_SELECTOR, "#memberRows input[name='new_member_password'][type='password']").is_displayed()
    assert driver.find_element(By.CSS_SELECTOR, "#admin-users input[name='dependent_name']").is_displayed()
    assert driver.find_element(By.CSS_SELECTOR, "#admin-users select[name='dependent_is_minor']").is_displayed()

    assert driver.find_element(By.ID, "meal_child_menu").is_displayed()
    assert driver.find_element(By.ID, "meal_adult_price").get_attribute("value")
    assert driver.find_element(By.ID, "meal_child_price").get_attribute("value")
    assert driver.find_element(By.ID, "meal_at").get_attribute("value")
    assert driver.find_element(By.ID, "meal_reservation_deadline_at").get_attribute("value")

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
