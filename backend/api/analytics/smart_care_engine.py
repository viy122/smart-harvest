"""
Engine CLI: reads JSON from stdin, fetches weather, runs CropPredictor, writes JSON to stdout.
Input JSON (stdin) example:
 {"N":90,"P":40,"K":45,"ph":6.5,"city":"Balayan"}
Note: temperature and humidity are fetched from OpenWeather; rainfall may be missing -> default 0.
"""
import sys
import os
import json
import traceback

# Best-effort imports with helpful error messages returned as JSON
try:
    import requests
except Exception as e:
    print(json.dumps({"error": f"Missing python dependency 'requests': {str(e)}"}))
    sys.exit(1)

try:
    from smart_care_model import CropPredictor
except Exception as e:
    print(json.dumps({"error": f"Failed to import model wrapper: {str(e)}"}))
    sys.exit(1)

OPENWEATHER_API_KEY = os.environ.get("OPENWEATHER_API_KEY", "4cac84b627ac52ac5a76e3b3e2349132").strip()
OPENWEATHER_URL = "https://api.openweathermap.org/data/2.5/weather"

def get_weather_by_city(city):
    if not OPENWEATHER_API_KEY:
        raise RuntimeError("OPENWEATHER_API_KEY not set in environment")
    params = {"q": city, "appid": OPENWEATHER_API_KEY, "units": "metric"}
    r = requests.get(OPENWEATHER_URL, params=params, timeout=10)
    r.raise_for_status()
    return r.json()

def extract_weather(ow_json):
    # temperature (C), humidity (%), rainfall (mm)
    main = ow_json.get("main", {})
    temp = main.get("temp")
    humidity = main.get("humidity")
    rain = ow_json.get("rain", {}) or {}
    # openweather may provide '1h' or '3h' rainfall
    rainfall = rain.get("1h", rain.get("3h", 0)) or 0
    # Make sure numeric
    temp = float(temp) if temp is not None else None
    humidity = float(humidity) if humidity is not None else None
    rainfall = float(rainfall) if rainfall is not None else 0.0
    return {"temperature": temp, "humidity": humidity, "rainfall": rainfall}

def safe_float(v, name):
    try:
        return float(v)
    except Exception:
        raise ValueError(f"Invalid numeric value for {name}: {v}")

def main():
    try:
        raw = sys.stdin.read()
        if not raw or raw.strip() == "":
            print(json.dumps({"error": "No input provided on stdin"}))
            return

        inp = json.loads(raw)
        # Required soil inputs: N,P,K,ph. city optional but used for weather.
        if not all(k in inp for k in ("N", "P", "K", "ph")):
            print(json.dumps({"error": "Missing required inputs (N,P,K,ph)"}))
            return

        N = safe_float(inp.get("N"), "N")
        P = safe_float(inp.get("P"), "P")
        K = safe_float(inp.get("K"), "K")
        ph = safe_float(inp.get("ph"), "ph")
        city = inp.get("city", "") or inp.get("city_name", "")

        # Fetch weather by city if provided
        weather = {"temperature": None, "humidity": None, "rainfall": 0.0}
        if city:
            try:
                ow = get_weather_by_city(city)
                weather = extract_weather(ow)
            except Exception as e:
                # If weather fetch fails, include informative error but continue using defaults where possible
                print(json.dumps({"error": f"Weather fetch failed: {str(e)}", "details": traceback.format_exc()}))
                return

        # Fill defaults for temperature/humidity if missing
        if weather["temperature"] is None or weather["humidity"] is None:
            print(json.dumps({"error": "Weather API did not return temperature/humidity"}))
            return

        # Build features according to model: [N,P,K,temperature,humidity,ph,rainfall]
        features = [N, P, K, weather["temperature"], weather["humidity"], ph, weather["rainfall"]]

        # Predict
        try:
            predictor = CropPredictor()
        except Exception as e:
            print(json.dumps({"error": f"Failed to load model: {str(e)}"}))
            return

        try:
            label, probs = predictor.predict_crop(features)
        except Exception as e:
            print(json.dumps({"error": f"Prediction error: {str(e)}", "trace": traceback.format_exc()}))
            return

        out = {
            "recommended_crop": label,
            "probabilities": probs,
            "features": {
                "N": N, "P": P, "K": K, "temperature": weather["temperature"],
                "humidity": weather["humidity"], "ph": ph, "rainfall": weather["rainfall"]
            }
        }
        print(json.dumps(out))
    except Exception as e:
        print(json.dumps({"error": f"Engine failure: {str(e)}", "trace": traceback.format_exc()}))

if __name__ == "__main__":
    main()
