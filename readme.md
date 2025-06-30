# Google Calendar PHP Integration

This project is a PHP web application that allows users to connect their Google Calendar, view their calendars and events, and create new events. It uses the Google API PHP Client and provides a modern, interactive user interface built with Bootstrap and FullCalendar.js.

## Features

*   OAuth 2.0 authentication with Google.
*   View a list of all your Google Calendars.
*   Display events from a selected calendar in a full-sized, interactive calendar view (Month, Week, Day).
*   Create new events in any of your calendars.
*   Modern and responsive UI.

## Requirements

*   PHP 8.0 or higher
*   [Composer](https://getcomposer.org/) for dependency management
*   A Google Account
*   A Google Cloud Platform project with the Google Calendar API enabled

## Setup Instructions

### 1. Clone the Repository

First, clone this repository to your local machine.

```bash
git clone 
cd <repository-directory>
```

### 2. Install Dependencies

Run Composer to install the required PHP libraries specified in `composer.json`.

```bash
composer install
```

### 3. Set up Google API Credentials

To interact with the Google Calendar API, you need to create credentials in the Google Cloud Platform.

1.  **Go to the Google Cloud Console:** [https://console.cloud.google.com/](https://console.cloud.google.com/)
2.  **Create a new project** or select an existing one.
3.  **Enable the Google Calendar API:**
    *   In the navigation menu, go to **APIs & Services > Library**.
    *   Search for "Google Calendar API" and click **Enable**.
4.  **Configure the OAuth Consent Screen:**
    *   Go to **APIs & Services > OAuth consent screen**.
    *   Choose **External** and click **Create**.
    *   Fill in the required application details (app name, user support email, developer contact).
    *   On the **Scopes** page, you don't need to add any scopes here; the application will request them dynamically.
    *   On the **Test users** page, click **+ ADD USERS** and add the Google Account(s) you will use to test the application.
    *   Save and continue.
5.  **Create Credentials:**
    *   Go to **APIs & Services > Credentials**.
    *   Click **+ CREATE CREDENTIALS** and select **OAuth client ID**.
    *   For **Application type**, select **Web application**.
    *   Give it a name (e.g., "Calendar App Web Client").
    *   Under **Authorized redirect URIs**, click **+ ADD URI** and enter the following URL:
        ```
        http://localhost:8000/oauth2callback.php
        ```
    *   Click **Create**.
6.  **Download the Credentials:**
    *   A window will pop up showing your Client ID and Client Secret. Click **DOWNLOAD JSON**.
    *   Rename the downloaded file to `client_secret.json`.
    *   Place this `client_secret.json` file in the root directory of the project.

**IMPORTANT:** The `client_secret.json` file contains sensitive information. Do not commit it to a public Git repository. The `.gitignore` file in this project is already configured to ignore it.

### 4. Run the Application

Use the built-in PHP web server to run the application.

```bash
php -S localhost:8000
```

Now, open your web browser and navigate to:

[http://localhost:8000](http://localhost:8000)

You will be prompted to log in with your Google account, and then you can start managing your calendars.