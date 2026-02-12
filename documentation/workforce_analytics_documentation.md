# Workforce Analytics & Forecasting Module Documentation

## 1. Project Goal & Impact
**Objective:** To analyze historical skill data, identify demand trends and workforce shortages, and provide data-driven insights for planning.
**Target Audience:** Organizations like **PESO (Public Employment Service Office)**.
**Key Value:** Helps decision-makers answer: *"What skills are in high demand next month?"* and *"Where do we have a shortage of workers?"*

---

## 2. The Machine Learning Model Strategies
We evaluated LSTM (too complex/data-heavy) and selected **ARIMA** as the optimal "Smart" model for this Capstone.

### Strategy: ARIMA (AutoRegressive Integrated Moving Average)
*   **Why:** It is the industry standard for time-series forecasting. It handles seasonality (e.g., spikes in demand during specific months) better than simple linear regression.
*   **Dynamic Nature:** The model is not static. It uses a **"Rolling Window"** approach. Every month, as new real-world data comes in (e.g., actual January request counts), the model includes this new data to refine its prediction for February. It "learns" from the immediate past to correct its future trajectory.
*   **Architecture:** **PHP - Python Bridge**
    *   **Laravel (PHP)**: Manages data, user requests, and display.
    *   **Python**: Runs the actual statistical calculation using `statsmodels` library.
    *   **Bridge**: Laravel calls a Python script (`arima_forecast.py`) passing JSON data, and Python returns the forecast.

---

## 3. Required Components
To build this feature, the system needs the following components:

### A. The "Engine" (Backend)
1.  **Python Environment**: Installed on the server with `pandas` and `statsmodels` libraries.
2.  **Interface Script**: A Python script (`arima_forecast.py`) that accepts historical data as input and outputs a prediction.
3.  **Laravel Controller**: A controller that queries the database, formats the data, executes the Python script, and captures the result.

### B. The "Visuals" (Frontend/Reporting)
1.  **Admin Dashboard**: A view showing "Projected Demand" vs "Current Supply".
2.  **Report Generator**:
    *   **Library:** `PHPOffice/PHPWord` for generating downloadable .docx files.
    *   **Charting:** `QuickChart.io` (or similar) to generate static chart images for the Word document.

---

## 4. The Datasets (Input Data)
For the model to work, we need to restructure and aggregate data.

### ⚠️ Critical Database Change
**Action Required:** specific skill tracking is missing in `service_requests`.
*   **Current State:** `service_requests` links to `provider_id` (User) but not the specific `skill_id`.
*   **Fix:** Add a `skill_id` column to the `service_requests` table to track exactly *what* skill was demanded.

### Data Aggregation (Transform Data for AI)
The AI cannot read raw user rows. It needs time-series data.
**Example "Demand" Dataset:**
| Month | Skill | Location | Total Requests (Demand) |
| :--- | :--- | :--- | :--- |
| Jan 2025 | Plumbing | Manila | 45 |
| Feb 2025 | Plumbing | Manila | 48 |
| Mar 2025 | Plumbing | Manila | 55 |

**Example "Supply" Dataset:**
| Month | Skill | Location | Active Workers (Supply) |
| :--- | :--- | :--- | :--- |
| Jan 2025 | Plumbing | Manila | 40 |
| Feb 2025 | Plumbing | Manila | 42 |

### The "Gap" (What we analyze)
`Gap = Total Requests (Demand) - Active Workers (Supply)`
*   **Positive Gap:** Shortage (Hire more!)
*   **Negative Gap:** Surplus (Too many workers)

---

## 5. Output & Deliverables
What the user/admin actually sees and gets.

### A. The Insight (Dashboard Widget)
> **Skill:** Plumbing
> **Next Month Forecast:** 62 Requests 📈
> **Current Capacity:** 42 Workers
> **Status:** 🔴 **CRITICAL SHORTAGE** (Deficit: 20)

### B. The Document (admin_report.docx)
A generated Word file containing:
1.  **Executive Summary:** "Plumbing services are projected to grow by 12%..."
2.  **Visual Charts:** Line graph of Past Demand + Future Prediction.
3.  **Strategic Recommendation:** "Launch training program for 20 new plumbers in Manila area."

## 6. Ensuring Accuracy & Success
How do we prove the model works?

### A. The "Golden Rule": Data Quality
**"Garbage In, Garbage Out."**
*   **Consistency:** Ensure `skill_id` is always recorded. No generic "Service Request" entries.
*   **Volume:** You need at least **12-24 months of data points** for decent accuracy. Since this is a new system, you must **generate realistic dummy data** for the past 2 years to train the model during your defense.

### B. Validation Metrics (Small Data Strategy)
Since you only have **6 months of data**, the standard 80/20 rule won't work well (you can't train on 4 months and test on 1). Instead, use **"Last-Month Validation"**:

1.  **The Test:**
    *   **Training:** Fed the model Months 1-5 (Jan - May).
    *   **Testing:** Allowed the model to predict Month 6 (June).
    *   **Verification:** Compare the Model's "June Prediction" vs. the "Actual June Data".

2.  **Synthetic Stress Testing (Optional but Recommended):**
    *   For the purpose of the *thesis study only*, simulate a longer timeline (e.g., repeating the 6-month pattern twice) to test stability.
    *   *Note:* Clearly state in your paper: "Due to limited operational data, synthetic extrapolation was used to validate model stability over a 12-month period."

**Thesis Statement Example:**
*"Using a 6-month historical dataset, the ARIMA model was validated using a Last-Month Holdout method, achieving an accuracy of 92% in predicting the final month's demand."*

## 7. Model Training & Deployment Workflow (Google Colab)
Since you are training in **Google Colab**, the workflow changes slightly to an "Offline Training" model:

1.  **Export Data:**
    *   Laravel Controller exports historical data to `training_data.csv`.
2.  **Train in Colab:**
    *   Upload `training_data.csv` to Colab.
    *   Run ARIMA notebook to train and tune the model.
    *   **Export Model:** Save the trained model as a file (e.g., `arima_model.pkl`).
3.  **Deploy to Laravel:**
    *   Download `arima_model.pkl` and place it in your Laravel project folder.
    *   Your Python script (on the server) simply loads this pre-trained file to make predictions.
    *   *Benefit:* This is much faster and more stable for production than training on every request.
4.  **Monthly Update Cycle (Dynamic Learning):**
    *   **Trigger:** At the end of every month (e.g., via a Laravel Scheduler/Cron Job).
    *   **Action:** The system automatically re-exports the latest data (including the month just finished) and re-runs the training script.
    *   **Result:** The `arima_model.pkl` is updated. This ensures the model adapts if trends suddenly change (e.g., a sudden heatwave increasing AC repair demand unpredictably).

## 8. Reality Check: The "99% Accuracy" Trap
**Do NOT promise 99% accuracy.**
*   **The Trap:** In Machine Learning, 99% accuracy usually means the model is **Overfitting** (it memorized the past but can't predict the future).
*   **The Industry Standard:** For business forecasting, **80-90% accuracy** is considered excellent.
*   **Your Defense Line:** "Our model achieved 88% accuracy, which is highly reliable for strategic planning while avoiding the overfitting risks associated with higher theoretical scores."
