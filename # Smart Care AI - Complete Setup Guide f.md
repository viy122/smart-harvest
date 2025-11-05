# Smart Care AI - Complete Setup Guide for Windows

This guide will help you set up the Smart Care crop recommendation system on any Windows machine running XAMPP.

## Prerequisites
- Windows 10/11
- XAMPP installed (with Apache and MySQL)
- Administrator access

---

## Step 1: Install Python 3.10.11
Download: https://www.python.org/downloads/release/python-31011/
- ✅ CHECK "Add Python 3.10 to PATH"
- ✅ CHECK "Install for all users"

Verify:
```cmd
python --version
```
Should output: `Python 3.10.11`

---

## Step 2: Install Python Libraries

**Run CMD as Administrator:**
```cmd
cd c:\xampp\htdocs\Agrilink\backend\api\analytics
python -m pip install --upgrade pip
python -m pip install numpy==1.24.4 pandas==2.0.3 scikit-learn==1.3.2 joblib==1.3.2 requests==2.31.0 scipy==1.10.1
```

Verify:
```cmd
python -m pip list
```

**Expected output:**
```
Package           Version
----------------- -------
joblib            1.3.2
numpy             1.24.4
pandas            2.0.3
requests          2.31.0
scikit-learn      1.3.2
scipy             1.10.1
```

---

## Step 3: Set Windows Permissions

**Run PowerShell AS ADMINISTRATOR:**
```powershell
icacls "C:\Windows\Temp" /grant "IUSR:(OI)(CI)F" /T
icacls "C:\Windows\Temp" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\Windows\Temp" /grant "Users:(OI)(CI)F" /T
icacls "C:\Program Files\Python310" /grant "IUSR:(OI)(CI)RX" /T
icacls "C:\Program Files\Python310" /grant "IIS_IUSRS:(OI)(CI)RX" /T
icacls "c:\xampp\htdocs\Agrilink\backend\api\analytics" /grant "IUSR:(OI)(CI)F" /T
icacls "c:\xampp\htdocs\Agrilink\backend\api\analytics" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "c:\xampp\htdocs\Agrilink\backend\api\analytics" /grant "Users:(OI)(CI)F" /T
```

**If errors, use CMD AS ADMINISTRATOR instead:**
```cmd
icacls "C:\Windows\Temp" /grant IUSR:(OI)(CI)F /T
icacls "C:\Program Files\Python310" /grant IUSR:(OI)(CI)RX /T
icacls "c:\xampp\htdocs\Agrilink\backend\api\analytics" /grant IUSR:(OI)(CI)F /T
```

---

## Step 4: Set API Key

**Run CMD:**
```cmd
setx OPENWEATHER_API_KEY "4cac84b627ac52ac5a76e3b3e2349132" /M
```

Then restart XAMPP Apache.

---

## Step 5: Verify Python Path in recommendCrop.php

Check line 8:
```php
$python = 'C:\\Program Files\\Python310\\python.exe';
```

If Python installed elsewhere, find it:
```cmd
where python
```

---

## Step 6: Train Model

```cmd
cd c:\xampp\htdocs\Agrilink\backend\api\analytics
python train_crop_model.py --csv crop_recommendation.csv
```

---

## Step 7: Test

```cmd
echo {"N":90,"P":40,"K":45,"ph":6.5,"city":"Balayan"} | python smart_care_engine.py
```

Done! 🌾

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