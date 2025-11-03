"""
Train a simple GaussianNB crop recommendation model and save it with LabelEncoder.
Usage:
  python train_crop_model.py --csv path/to/crop_recommendation.csv
"""
import os
import argparse
import json
import joblib
import numpy as np
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.naive_bayes import GaussianNB
from sklearn.metrics import accuracy_score, classification_report

DEFAULT_CSV = os.path.join(os.path.dirname(__file__), "crop_recommendation.csv")
MODEL_OUT = os.path.join(os.path.dirname(__file__), "crop_recommendation_model.pkl")

def main():
    parser = argparse.ArgumentParser(description="Train crop recommendation model")
    parser.add_argument("--csv", default=DEFAULT_CSV, help="Path to crop_recommendation.csv")
    parser.add_argument("--out", default=MODEL_OUT, help="Output model pkl file")
    args = parser.parse_args()

    if not os.path.exists(args.csv):
        print(json.dumps({"error": f"CSV file not found: {args.csv}"}))
        return

    df = pd.read_csv(args.csv)
    required_cols = ["N", "P", "K", "temperature", "humidity", "ph", "rainfall", "label"]
    missing = [c for c in required_cols if c not in df.columns]
    if missing:
        print(json.dumps({"error": f"Missing columns in CSV: {missing}"}))
        return

    X = df[["N", "P", "K", "temperature", "humidity", "ph", "rainfall"]].astype(float).values
    y = df["label"].astype(str).values

    le = LabelEncoder()
    y_enc = le.fit_transform(y)

    X_train, X_val, y_train, y_val = train_test_split(X, y_enc, test_size=0.2, random_state=42, stratify=y_enc)

    clf = GaussianNB()
    clf.fit(X_train, y_train)

    y_pred = clf.predict(X_val)
    acc = accuracy_score(y_val, y_pred)
    report = classification_report(y_val, y_pred, target_names=le.classes_, zero_division=0)

    # Save model + label encoder
    joblib.dump({"model": clf, "label_encoder": le}, args.out)

    print(json.dumps({
        "status": "ok",
        "model_path": os.path.abspath(args.out),
        "validation_accuracy": float(acc),
        "classification_report": report
    }, indent=2))

if __name__ == "__main__":
    main()
