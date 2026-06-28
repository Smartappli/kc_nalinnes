import os
from pathlib import Path

import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options as ChromeOptions


def pytest_addoption(parser):
    parser.addoption(
        "--base-url",
        action="store",
        default=os.environ.get("BASE_URL", "http://127.0.0.1:8000"),
        help="Base URL for the running KC Nalinnes site.",
    )


@pytest.fixture(scope="session")
def base_url(pytestconfig):
    return pytestconfig.getoption("--base-url").rstrip("/")


@pytest.fixture
def driver(request):
    options = ChromeOptions()
    chrome_bin = os.environ.get("CHROME_BIN")
    if chrome_bin:
        options.binary_location = chrome_bin

    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")
    options.add_argument("--window-size=1440,1100")
    options.page_load_strategy = "eager"

    remote_url = os.environ.get("SELENIUM_REMOTE_URL")
    if remote_url:
        browser = webdriver.Remote(command_executor=remote_url, options=options)
    else:
        browser = webdriver.Chrome(options=options)

    browser.set_page_load_timeout(30)
    request.node.driver = browser

    yield browser

    browser.quit()


@pytest.hookimpl(hookwrapper=True)
def pytest_runtest_makereport(item, call):
    outcome = yield
    report = outcome.get_result()

    if report.when != "call" or not report.failed:
        return

    browser = getattr(item, "driver", None)
    if browser is None:
        return

    artifact_dir = Path(os.environ.get("SELENIUM_ARTIFACT_DIR", "artifacts/selenium"))
    artifact_dir.mkdir(parents=True, exist_ok=True)

    safe_name = item.name.replace("/", "_").replace("\\", "_")
    browser.save_screenshot(str(artifact_dir / f"{safe_name}.png"))
    (artifact_dir / f"{safe_name}.html").write_text(browser.page_source, encoding="utf-8")
