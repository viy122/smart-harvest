# Smart Care AI - Complete Setup Guide for Windows

This guide will help you set up the Smart Care crop recommendation system on any Windows machine running XAMPP.

## Prerequisites
- Windows 10/11
- XAMPP installed (with Apache and MySQL)
- Administrator access

---

## Step 1: Install Python 3.10.11

1. Download Python 3.10.11 from [python.org](https://www.python.org/downloads/release/python-31011/)
2. Run the installer
3. **IMPORTANT**: Check "Add Python to PATH" during installation
4. Install to default location: `C:\Program Files\Python310\`
5. Verify installation:
   ```batch
   python --version
   ```
   Should output: `Python 3.10.11`

---

## Step 2: Install Python Dependencies

1. Open Command Prompt as Administrator
2. Navigate to the analytics folder:
   ```batch
   cd c:\xampp\htdocs\Agrilink\backend\api\analytics
   ```

3. Create `requirements.txt` file in this folder with the following content:
   ```text
   numpy==1.24.4
   scipy==1.10.1
   scikit-learn==1.3.2
   pandas==2.0.3
   joblib==1.3.2
   requests==2.31.0
   ```

4. Install dependencies:
   ```batch
   "C:\Program Files\Python310\python.exe" -m pip install --upgrade pip
   "C:\Program Files\Python310\python.exe" -m pip install -r requirements.txt
   ```

5. Verify installation:
   ```batch
   "C:\Program Files\Python310\python.exe" -m pip list
   ```

---

## Step 3: Set Windows Permissions (Critical!)

Open Command Prompt as Administrator and run:

```batch
REM Grant write permissions to Windows Temp folder
icacls "C:\Windows\Temp" /grant "IUSR:(OI)(CI)F" /T
icacls "C:\Windows\Temp" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\Windows\Temp" /grant "Users:(OI)(CI)F" /T

REM Grant permissions to Python installation
icacls "C:\Program Files\Python310" /grant "IUSR:(OI)(CI)RX" /T
icacls "C:\Program Files\Python310" /grant "IIS_IUSRS:(OI)(CI)RX" /T

REM Grant permissions to analytics folder
icacls "c:\xampp\htdocs\Agrilink\backend\api\analytics" /grant "IUSR:(OI)(CI)F" /T
icacls "c:\xampp\htdocs\Agrilink\backend\api\analytics" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

---

## Step 4: Set Environment Variables

### Option A: System-wide (Recommended)

1. Open Start Menu → Search "Environment Variables"
2. Click "Edit the system environment variables"
3. Click "Environment Variables" button
4. Under "System variables", click "New"
5. Add the following variables:
   - Variable name: `OPENWEATHER_API_KEY`
   - Variable value: `4cac84b627ac52ac5a76e3b3e2349132`
6. Click OK and restart XAMPP

### Option B: Apache-specific

Edit `c:\xampp\apache\conf\extra\httpd-xampp.conf` and add:
```apache
SetEnv OPENWEATHER_API_KEY "4cac84b627ac52ac5a76e3b3e2349132"
```

Then restart Apache.

---

## Step 5: Verify File Structure

Ensure these files exist in `c:\xampp\htdocs\Agrilink\backend\api\analytics\`:

```
analytics/
├── crop_recommendation.csv          (your dataset)
├── train_crop_model.py              (training script)
├── smart_care_model.py              (model wrapper class)
├── smart_care_engine.py             (prediction engine)
├── recommendCrop.php                (PHP API endpoint)
├── requirements.txt                 (Python dependencies)
└── crop_recommendation_model.pkl    (will be created in Step 6)
```

---

## Step 6: Train the Model (One-time setup)

1. Open Command Prompt
2. Navigate to analytics folder:
   ```batch
   cd c:\xampp\htdocs\Agrilink\backend\api\analytics
   ```

3. Run training script:
   ```batch
   "C:\Program Files\Python310\python.exe" train_crop_model.py --csv crop_recommendation.csv
   ```

4. You should see output like:
   ```json
   {
     "status": "ok",
     "model_path": "C:\\xampp\\htdocs\\Agrilink\\backend\\api\\analytics\\crop_recommendation_model.pkl",
     "validation_accuracy": 0.99,
     "classification_report": "..."
   }
   ```

5. Verify `crop_recommendation_model.pkl` was created

---

## Step 7: Test Python Script Directly

Test the engine from command line:

```batch
cd c:\xampp\htdocs\Agrilink\backend\api\analytics
echo {"N":90,"P":40,"K":45,"ph":6.5,"city":"Balayan"} | "C:\Program Files\Python310\python.exe" smart_care_engine.py
```

Expected output (JSON):
```json
{
  "recommended_crop": "rice",
  "probabilities": {
    "rice": 0.95,
    "wheat": 0.03,
    ...
  },
  "features": {
    "N": 90,
    "P": 40,
    "K": 45,
    "temperature": 28.5,
    "humidity": 80,
    "ph": 6.5,
    "rainfall": 200
  }
}
```

---

## Step 8: Test PHP Endpoint

1. Start XAMPP (Apache must be running)
2. Open browser and go to:
   ```
   http://localhost/Agrilink/backend/api/analytics/recommendCrop.php
   ```
3. Use browser console or Postman to send POST request:
   ```javascript
   fetch('http://localhost/Agrilink/backend/api/analytics/recommendCrop.php', {
     method: 'POST',
     headers: { 'Content-Type': 'application/json' },
     body: JSON.stringify({
       N: 90,
       P: 40,
       K: 45,
       ph: 6.5,
       city: 'Balayan'
     })
   })
   .then(r => r.json())
   .then(data => console.log(data));
   ```

4. Check `debug_php.log` if you get errors:
   ```batch
   type c:\xampp\htdocs\Agrilink\backend\api\analytics\debug_php.log
   ```

---

## Step 9: Test Frontend

1. Open browser: `http://localhost/Agrilink/layout.php?page=analytics`
2. Navigate to "Smart Care" tab
3. Fill in soil values:
   - Nitrogen (N): 90
   - Phosphorus (P): 40
   - Potassium (K): 45
   - Soil pH: 6.5
4. Click "Run Smart Care AI"
5. You should see the recommendation with probabilities

---

## Troubleshooting

### Error: "Python executable not found"
- Verify Python path: `dir "C:\Program Files\Python310\python.exe"`
- Update path in `recommendCrop.php` line 8

### Error: "Failed to get random numbers to initialize Python"
- Run the `icacls` commands from Step 3 again
- Restart Apache after running commands

### Error: "Module not found" (numpy, sklearn, etc.)
- Reinstall dependencies: `"C:\Program Files\Python310\python.exe" -m pip install -r requirements.txt`
- Use full Python path, not just `python`

### Error: "Empty response from Python engine"
- Check `debug_php.log` for Python errors
- Test Python script directly (Step 7)
- Verify model file exists: `dir crop_recommendation_model.pkl`

### Error: "Weather fetch failed"
- Check internet connection
- Verify API key in environment variables
- Try hardcoded API key in `recommendCrop.php` line 118

### Frontend shows spinning loader forever
- Open browser DevTools (F12) → Console tab
- Check for JavaScript errors
- Check Network tab for HTTP response
- Look at `debug_php.log`

---

## Performance Tips

1. **Cache weather data**: Store in session/database for 15 minutes
2. **Pre-load model**: Keep Python process running (FastCGI/mod_wsgi)
3. **Batch predictions**: Process multiple requests in single Python call

---

## Security Notes for Production

1. **Remove debug logging**: Comment out `debug_php.log` writes
2. **Hide API key**: Use environment variable, never hardcode in PHP
3. **Validate inputs**: Add min/max ranges for N, P, K, pH
4. **Rate limiting**: Prevent API abuse
5. **HTTPS only**: Force SSL for API requests

---

## File Checklist

Before deploying to another machine, ensure you have:

- [ ] `crop_recommendation.csv` (dataset)
- [ ] `train_crop_model.py`
- [ ] `smart_care_model.py`
- [ ] `smart_care_engine.py`
- [ ] `recommendCrop.php`
- [ ] `requirements.txt`
- [ ] `crop_recommendation_model.pkl` (or train on new machine)

---

## Contact

For issues or questions, check:
- Browser console (F12)
- `c:\xampp\htdocs\Agrilink\backend\api\analytics\debug_php.log`
- `c:\xampp\apache\logs\error.log`

Good luck! 🌾