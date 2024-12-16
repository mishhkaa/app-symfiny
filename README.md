# Cryptocurrency Rates API

## Overview
This project is a web application that provides an API to retrieve cryptocurrency exchange rates for specific currency pairs (e.g., BTC/USD, BTC/EUR). The application fetches data from Binance API, stores it in a MySQL database, and allows users to filter data based on a time range.

## Features
- Retrieve cryptocurrency rates via API.
- Filter rates by currency pair and time range.
- Periodically update rates from Binance using a console command.
- Supports at least 3 currency pairs: BTC/USD, BTC/EUR, BTC/GBP.

## Technologies Used
- Symfony 6
- PHP 8.x
- MySQL
- Binance API

## Requirements
- PHP 8.x
- Composer
- MySQL
- Symfony CLI

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo.git
   cd your-repo
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Configure the `.env` file:
   ```bash
   cp .env .env.local
   ```
   Update the `DATABASE_URL` to your database connection:
   ```env
   DATABASE_URL="mysql://root:root@127.0.0.1:3306/my_project?serverVersion=8.0&charset=utf8mb4"
   ```

4. Set up the database:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. Start the server:
   ```bash
   symfony serve
   ```
   Access the application at `http://localhost:8000`.

## API Usage
### Retrieve Rates
- **Endpoint:** `/api/rates`
- **Method:** `GET`
- **Parameters:**
  - `currencyPair` (required): Currency pair (e.g., BTC/USD).
  - `start` (optional): Start date in `YYYY-MM-DD` format.
  - `end` (optional): End date in `YYYY-MM-DD` format.

#### Example Request
```bash
http://localhost:8000/api/rates?currencyPair=BTC/USD&start=2024-12-01&end=2024-12-10
```
#### Example Response
```json
[
    {
        "id": 1,
        "currencyPair": "BTC/USD",
        "rate": 50000,
        "timestamp": "2024-12-14 13:17:03"
    }
]
```

## Update Rates
Run the following command to fetch and update the rates:
```bash
php bin/console app:update-rates
```

## Automating Updates
To automate updates, use a CRON job:
```bash
* * * * * /usr/bin/php /path/to/your/project/bin/console app:update-rates >> /path/to/your/project/cron_output.log 2>&1
```
- Replace `/path/to/your/project` with the actual project path.
- Logs are stored in `cron_output.log` for debugging.

## Testing
1. Test API endpoints via Postman or cURL:
   ```bash
   http://localhost:8000/api/rates?currencyPair=BTC/USD&start=2024-12-01&end=2024-12-10
   ```

2. Test the update command:
   ```bash
   php bin/console app:update-rates
   ```

3. Check the database for updated records:
   ```sql
   SELECT * FROM currency_rate ORDER BY timestamp DESC;
   ```

## Repository Structure
- `src/Controller`: API controllers.
- `src/Entity`: Database entities.
- `src/Command`: Custom console commands.
- `config`: Configuration files.
- `public`: Entry point and assets.

## Contact
For issues, email [your-email@example.com](mailto:your-email@example.com).

