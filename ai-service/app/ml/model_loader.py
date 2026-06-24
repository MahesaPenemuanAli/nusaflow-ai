import os
import json
import joblib

MODEL_PATH = 'saved_models/crowd_prediction_model.joblib'
METADATA_PATH = 'saved_models/model_metadata.json'

_model = None
_metadata = None

def model_exists() -> bool:
    return os.path.exists(MODEL_PATH)

def load_crowd_model():
    global _model
    if _model is not None:
        return _model
        
    if not model_exists():
        return None
        
    try:
        _model = joblib.load(MODEL_PATH)
        return _model
    except Exception as e:
        print(f"Error loading model: {e}")
        return None

def load_model_metadata() -> dict | None:
    global _metadata
    if _metadata is not None:
        return _metadata
        
    if not os.path.exists(METADATA_PATH):
        return None
        
    try:
        with open(METADATA_PATH, 'r') as f:
            _metadata = json.load(f)
        return _metadata
    except Exception as e:
        print(f"Error loading metadata: {e}")
        return None
