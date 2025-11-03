"""
Model wrapper - CropPredictor
Loads crop_recommendation_model.pkl saved by train_crop_model.py
Provides predict_crop(features) -> (label, prob_map)
"""
import os
import joblib
import numpy as np

DEFAULT_MODEL = os.path.join(os.path.dirname(__file__), "crop_recommendation_model.pkl")

class CropPredictor:
    def __init__(self, model_path=None):
        self.model_path = model_path or DEFAULT_MODEL
        if not os.path.exists(self.model_path):
            raise FileNotFoundError(f"Model file not found: {self.model_path}")
        data = joblib.load(self.model_path)
        # Expect a dict with keys "model" and "label_encoder"
        self.model = data.get("model")
        self.le = data.get("label_encoder", None)
        if self.model is None:
            raise ValueError("Model object missing inside pkl file")

    def predict_crop(self, features):
        """
        features: list or array-like of 7 numeric values:
          [N, P, K, temperature, humidity, ph, rainfall]
        returns: (predicted_label:str, probabilities:dict)
        """
        arr = np.array(features, dtype=float).reshape(1, -1)
        pred_idx = self.model.predict(arr)[0]
        probs = self.model.predict_proba(arr)[0]

        if self.le is not None:
            classes = list(self.le.inverse_transform(range(len(self.le.classes_))))
            # joblib stores LabelEncoder with .classes_ attribute; ensure mapping
            class_names = list(self.le.classes_)
            # map class index to label via inverse_transform on each class index to be safe
            class_labels = [str(c) for c in class_names]
            # If model returns encoded indices, map using label encoder
            try:
                pred_label = self.le.inverse_transform([pred_idx])[0]
            except Exception:
                pred_label = str(pred_idx)
        else:
            # If no label encoder, try using model.classes_
            class_labels = [str(c) for c in getattr(self.model, "classes_", [])]
            pred_label = str(pred_idx)

        prob_map = {}
        for i, p in enumerate(probs):
            key = class_labels[i] if i < len(class_labels) else str(i)
            prob_map[str(key)] = float(p)

        return pred_label, prob_map
