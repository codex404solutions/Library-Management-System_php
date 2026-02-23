@echo off
echo =====================================
echo Setting up the project...
echo =====================================

:: -------------------------------
:: Create virtual environment
:: -------------------------------
if not exist ".venv" (
    echo Creating virtual environment...
    python -m venv .venv
    echo [OK] .venv created
) else (
    echo [SKIP] .venv already exists
)

:: Activate virtual environment
call .venv\Scripts\activate
echo [OK] .venv activated

:: -------------------------------
:: Upgrade pip
:: -------------------------------
echo Upgrading pip...
python -m pip install --upgrade pip
if %errorlevel%==0 (
    echo [OK] pip upgraded
) else (
    echo [ERROR] pip upgrade failed
)

:: -------------------------------
:: Install dependencies
:: -------------------------------
if exist requirements.txt (
    echo Installing dependencies from requirements.txt...
    pip install -r requirements.txt
    if %errorlevel%==0 (
        echo [OK] requirements installed
    ) else (
        echo [ERROR] requirements installation failed
    )
) else (
    echo [SKIP] requirements.txt not found
)

:: -------------------------------
:: Create .gitignore
:: -------------------------------
if not exist ".gitignore" (
    echo Creating .gitignore...
    (
        echo .venv/
        echo __pycache__/
        echo *.pyc
        echo *.pyo
        echo *.log
        echo db.sqlite3
        echo .env
        echo .idea/
        echo .vscode/
        echo .DS_Store
        echo build/
        echo dist/
        echo *.egg-info/
        echo *.egg
        echo .pytest_cache/
        echo .coverage
        echo .mypy_cache/
        echo .tox/
        echo .coverage.*
        echo .ipynb_checkpoints/
        echo *.exe
        echo /SetupFile/
        echo .spec
    ) > .gitignore
    echo [OK] .gitignore created
) else (
    echo [SKIP] .gitignore already exists
)

:: -------------------------------
:: Create CODEOWNERS (ORG BASED)
:: -------------------------------
if not exist ".github\CODEOWNERS" (
    echo Creating CODEOWNERS file...
    mkdir .github 2>nul
    (
        echo # Code owners for this repository
        echo * @CodeX404Solutions/core-reviewers
    ) > .github\CODEOWNERS
    echo [OK] CODEOWNERS file created
) else (
    echo [SKIP] CODEOWNERS file already exists
)

:: -------------------------------
:: Create GitHub Action workflow
:: -------------------------------
if not exist ".github\workflows\update-readme.yml" (
    echo Creating GitHub Action workflow...
    mkdir .github\workflows 2>nul
    (
        echo name: Update README Folder Tree
        echo.
        echo on:
        echo   push:
        echo     branches:
        echo       - main
        echo   workflow_dispatch:
        echo.
        echo permissions:
        echo   contents: write
        echo.
        echo jobs:
        echo   update-readme:
        echo     runs-on: ubuntu-latest
        echo.
        echo     steps:
        echo       - name: Checkout repo
        echo         uses: actions/checkout@v4
        echo         with:
        echo           fetch-depth: 0
        echo.
        echo       - name: Set up Python
        echo         uses: actions/setup-python@v5
        echo         with:
        echo           python-version: '3.9'
        echo.
        echo       - name: Install dependencies
        echo         run: pip install -r requirements.txt ^|^| true
        echo.
        echo       - name: Update README.md with folder structure
        echo         run: python update_tree.py
        echo.
        echo       - name: Commit and push changes
        echo         env:
        echo           GITHUB_TOKEN: ${{^ secrets.GITHUB_TOKEN ^}}
        echo         run: ^|
        echo           git config --global user.name "mnoukhej"
        echo           git config --global user.email "mnoukhej@gmail.com"
        echo.
        echo           git add README.md
        echo           git diff-index --quiet HEAD ^|^| git commit -m "🔄 Auto-update folder structure in README.md"
        echo           git push
    ) > .github\workflows\update-readme.yml
    echo [OK] GitHub Action workflow created
) else (
    echo [SKIP] GitHub workflow already exists
)

echo =====================================
echo Setup complete!
echo =====================================
pause
