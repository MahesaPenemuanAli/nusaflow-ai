from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)

def test_model_info():
    response = client.get("/model-info")
    assert response.status_code == 200
    assert response.json()["success"] == True
    assert "model_available" in response.json()["data"]

def test_predict_crowd_ml_no_crash():
    payload = {
        "destination_id": 1,
        "destination_name": "Test Destination",
        "category": "Pantai",
        "max_capacity": 1000,
        "day_of_week": 5,
        "month": 6,
        "hour": 14,
        "is_weekend": True,
        "is_holiday": False,
        "has_event": False,
        "event_impact": "none",
        "weather": "clear",
        "temperature": 28.5,
        "rain_level": 0.0
    }
    response = client.post("/predict-crowd-ml", json=payload)
    assert response.status_code == 200
    
    json_resp = response.json()
    # It should either succeed (if model exists) or gracefully fail with specific message
    if json_resp["success"]:
        assert "predicted_count" in json_resp["data"]
        assert json_resp["data"]["method"] == "machine_learning"
    else:
        assert json_resp["message"] == "ML model is not available. Please train the model first."
