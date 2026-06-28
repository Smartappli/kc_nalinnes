import os
from urllib.parse import urljoin, urlparse

from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait


WAIT_SECONDS = 15


def env(name, default):
    return os.environ.get(name, default)


MEMBER_EMAIL = env("SELENIUM_MEMBER_EMAIL", "member.selenium@example.test")
MEMBER_PASSWORD = env("SELENIUM_MEMBER_PASSWORD", "MemberPass123!")
ADMIN_EMAIL = env("SELENIUM_ADMIN_EMAIL", "admin.selenium@example.test")
ADMIN_PASSWORD = env("SELENIUM_ADMIN_PASSWORD", "AdminPass123!")


def absolute_url(base_url, path):
    return urljoin(base_url + "/", path.lstrip("/"))


def wait_for_path(driver, expected_path):
    WebDriverWait(driver, WAIT_SECONDS).until(
        lambda current: urlparse(current.current_url).path == expected_path
    )


def wait_for_login_form(driver):
    return WebDriverWait(driver, WAIT_SECONDS).until(
        EC.visibility_of_element_located((By.ID, "email"))
    )


def submit_login(driver, base_url, email, password, remember=False):
    driver.get(absolute_url(base_url, "/membres.php?lang=fr"))
    email_input = wait_for_login_form(driver)
    password_input = driver.find_element(By.ID, "password")

    email_input.clear()
    email_input.send_keys(email)
    password_input.clear()
    password_input.send_keys(password)

    if remember:
        remember_box = driver.find_element(By.CSS_SELECTOR, "input[name='remember']")
        if not remember_box.is_selected():
            remember_box.click()

    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def logout(driver):
    logout_button = WebDriverWait(driver, WAIT_SECONDS).until(
        EC.element_to_be_clickable(
            (By.XPATH, "//form[.//input[@name='action' and @value='logout']]//button")
        )
    )
    logout_button.click()


def body_text(driver):
    return driver.find_element(By.TAG_NAME, "body").text


def test_login_page_renders_form_and_csrf_token(driver, base_url):
    driver.get(absolute_url(base_url, "/membres.php?lang=fr"))

    wait_for_login_form(driver)

    assert driver.find_element(By.ID, "password").is_displayed()
    assert driver.find_element(By.CSS_SELECTOR, "input[name='csrf_token']").get_attribute("value")
    assert urlparse(driver.current_url).path == "/membres.php"


def test_invalid_password_stays_on_login_and_preserves_email(driver, base_url):
    submit_login(driver, base_url, MEMBER_EMAIL, "wrong-password")

    wait_for_path(driver, "/membres.php")
    email_input = wait_for_login_form(driver)

    assert email_input.get_attribute("value") == MEMBER_EMAIL
    assert "incorrect" in driver.find_element(By.TAG_NAME, "body").text.lower()


def test_invalid_login_preserves_remember_choice(driver, base_url):
    submit_login(driver, base_url, MEMBER_EMAIL, "wrong-password", remember=True)

    wait_for_path(driver, "/membres.php")
    wait_for_login_form(driver)

    assert driver.find_element(By.CSS_SELECTOR, "input[name='remember']").is_selected()


def test_login_rejects_missing_csrf_token(driver, base_url):
    driver.get(absolute_url(base_url, "/membres.php?lang=fr"))
    wait_for_login_form(driver)

    driver.execute_script(
        """
        const form = document.createElement('form');
        form.method = 'post';
        form.action = arguments[0];
        for (const [name, value] of Object.entries({
            email: arguments[1],
            password: arguments[2],
            lang: 'fr'
        })) {
            const input = document.createElement('input');
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
        """,
        absolute_url(base_url, "/login_handler.php"),
        MEMBER_EMAIL,
        MEMBER_PASSWORD,
    )

    wait_for_path(driver, "/membres.php")
    wait_for_login_form(driver)

    assert "csrf" in body_text(driver).lower()


def test_member_dashboard_requires_authentication(driver, base_url):
    driver.get(absolute_url(base_url, "/member/dashboard.php?lang=fr"))

    wait_for_path(driver, "/membres.php")
    wait_for_login_form(driver)

    assert "connecter" in body_text(driver).lower()


def test_manager_dashboard_requires_authentication(driver, base_url):
    driver.get(absolute_url(base_url, "/manager/dashboard.php?lang=fr"))

    wait_for_path(driver, "/membres.php")
    wait_for_login_form(driver)

    assert "connecter" in body_text(driver).lower()


def test_member_can_login_reach_dashboard_and_logout(driver, base_url):
    submit_login(driver, base_url, MEMBER_EMAIL, MEMBER_PASSWORD)

    wait_for_path(driver, "/member/dashboard.php")
    assert MEMBER_EMAIL in body_text(driver)

    logout(driver)
    wait_for_path(driver, "/membres.php")
    wait_for_login_form(driver)


def test_member_is_redirected_away_from_manager_dashboard(driver, base_url):
    submit_login(driver, base_url, MEMBER_EMAIL, MEMBER_PASSWORD)
    wait_for_path(driver, "/member/dashboard.php")

    driver.get(absolute_url(base_url, "/manager/dashboard.php?lang=fr"))
    wait_for_path(driver, "/member/dashboard.php")

    assert MEMBER_EMAIL in body_text(driver)


def test_admin_can_login_to_manager_dashboard(driver, base_url):
    submit_login(driver, base_url, ADMIN_EMAIL, ADMIN_PASSWORD)

    wait_for_path(driver, "/manager/dashboard.php")
    body = body_text(driver)

    assert ADMIN_EMAIL in body
    assert MEMBER_EMAIL in body
