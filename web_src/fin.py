import sys
import os

# Add the project root directory to Python path
ROOT = os.path.dirname(os.path.dirname(__file__))
sys.path.append(ROOT)

from data_src.api.includes.db_connect import get_connection
import yfinance as yf
import mysql.connector

connection = get_connection()
cursor = connection.cursor()

cursor.execute("SELECT ticker FROM Holdings")
tickers = cursor.fetchall()

for (ticker,) in tickers:
    try:
        stock = yf.Ticker(ticker)
        price = stock.fast_info.get("lastPrice")

        if price is None:
            print(f"[WARN] Could not fetch price for {ticker}")
            continue

        cursor.execute(
            "UPDATE Holdings SET start_price = %s WHERE ticker = %s",
            (price, ticker)
        )
        print(f"[OK] Updated {ticker}: {price}")

    except Exception as e:
        print(f"[ERROR] {ticker}: {e}")

connection.commit()
cursor.close()
connection.close()

print("\nFinished.")
