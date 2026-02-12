# ARIMA Integration Guide: PHP - Python Bridge

This guide outlines how to implement the "Smart" forecasting feature using a Python script called from your Laravel application.

## 1. Prerequisites

You need Python installed on your server/environment.

```bash
# Check if python is installed
python --version

# Install necessary Python libraries
pip install pandas statsmodels
```

## 2. The Python Script (`arima_forecast.py`)

Create a script in your Laravel project root (e.g., `/python-scripts/arima_forecast.py`) that accepts JSON input and returns JSON output.

```python
import sys
import json
import pandas as pd
from statsmodels.tsa.arima.model import ARIMA
import warnings

# Suppress warnings
warnings.filterwarnings("ignore")

def forecast():
    # 1. Read input from arguments (passed by PHP)
    try:
        input_json = sys.argv[1]
        data = json.loads(input_json)
        
        # data format example: [{"date": "2024-01", "value": 10}, ...]
        df = pd.DataFrame(data)
        series = df['value']
        
        # 2. Train ARIMA Model
        # order=(p,d,q) -> (1,1,1) is a common starting point
        model = ARIMA(series, order=(1,1,1))
        model_fit = model.fit()
        
        # 3. Make Prediction (Next 1 step)
        forecast = model_fit.forecast(steps=1)
        prediction = float(forecast[0])
        
        # 4. Return Output as JSON
        result = {
            "status": "success",
            "prediction": round(prediction, 2),
            "message": "Forecast generated successfully"
        }
        print(json.dumps(result))

    except Exception as e:
        error = {
            "status": "error",
            "message": str(e)
        }
        print(json.dumps(error))

if __name__ == "__main__":
    forecast()
```

## 3. The Laravel Controller (`AnalyticsController.php`)

Use Symfony's `Process` component (built into Laravel) to run the Python script.

```php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

public function getForecast() {
    // 1. Prepare Data (Fetch from DB and format)
    // Example format: Just a list of values is easier specifically for this script version
    $historicalData = [
        ['date' => '2024-01', 'value' => 120],
        ['date' => '2024-02', 'value' => 135],
        ['date' => '2024-03', 'value' => 125],
        ['date' => '2024-04', 'value' => 140],
        ['date' => '2024-05', 'value' => 155],
        ['date' => '2024-06', 'value' => 160],
    ];
    
    $jsonData = json_encode($historicalData);

    // 2. Setup Process
    // Point to your python executable and the script
    $process = new Process(['python', base_path('python-scripts/arima_forecast.py'), $jsonData]);
    $process->run();

    // 3. Handle Errors
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    // 4. Get Output
    $output = $process->getOutput();
    $result = json_decode($output, true);

    if ($result['status'] === 'success') {
        return $result['prediction']; // e.g., 172.5
    } else {
        // Handle Python error
        return 0;
    }
}
```

## 4. Why this approach?
- **Separation of Concerns:** Python handles the math, PHP handles the business logic.
- **Robust:** If the Python script fails, your PHP app doesn't crash (you catch the exception).
- **Scalable:** You can easily swap the Python script for a more complex model later without changing your PHP code.

## 5. Example Outputs

Here is what the **Machine Learning Model** (Python script) will actually produce, and how you should display it in your Admin Dashboard or DOCX Report.

### A. Raw Output (from Python Script)
This is the hidden JSON data that your Laravel controller receives.

```json
{
  "status": "success",
  "prediction": 145,
  "confidence_lower": 130,
  "confidence_upper": 160,
  "trend": "increasing",
  "message": "Forecast generated successfully"
}
```

### B. User-Facing Output (Admin Dashboard)
Using the data above (`prediction: 145`), this is what the Admin sees:

> **Workforce Analytics for Plumbing Services**
>
> 🟢 **Projected Demand (Next Month):** **145 Requests**
> 📈 **Trend:** Increasing (+12% from last month)
> 💡 **AI Insight:** Demand is expected to exceed current workforce capacity (120 providers).
>
> **Action Recommended:**
> [Launch Training Program]  [Recruit More Plumbers]

### C. Generated Report (DOCX File)
In the downloadable report, you would present it formally:

> **3. Forecast Analysis**
>
> Based on historical data from Jan 2025 to Jun 2025, the ARIMA model predicts a continued upward trend in service requests.
>
> *   **Forecasted Volume:** 145 Service Requests
> *   **Confidence Interval:** 95% (Between 130 and 160 requests)
>
> **Strategic Recommendation:**
> The current supply of active providers (120) is insufficient to meet the projected demand of 145. Immediate recruitment or upskilling of existing workers is recommended to avoid service delays.
