from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)

def test_read_root():
    response = client.get("/")
    assert response.status_code == 200
    assert response.json()["success"] == True

def test_health_check():
    response = client.get("/health")
    assert response.status_code == 200
    assert response.json()["status"] == "healthy"

def test_predict_crowd():
    payload = {
        "destination_id": 1,
        "destination_name": "Pantai Test",
        "max_capacity": 1000,
        "visitor_count": 500,
        "prediction_date": "2026-06-24",
        "prediction_hour": 12,
        "is_weekend": False,
        "has_event": False,
        "event_impact": None,
        "weather": "clear"
    }
    response = client.post("/predict-crowd", json=payload)
    assert response.status_code == 200
    assert response.json()["success"] == True
    assert "crowd_score" in response.json()["data"]

def test_recommend_destinations():
    payload = {
        "current_destination_id": 1,
        "preferred_category": "Pantai",
        "avoid_crowded": True,
        "limit": 2,
        "candidates": [
            {
                "destination_id": 2,
                "name": "Pantai Damai",
                "category": "Pantai",
                "max_capacity": 1000,
                "visitor_count": 200,
                "is_active": True
            }
        ]
    }
    response = client.post("/recommend-destinations", json=payload)
    assert response.status_code == 200
    assert response.json()["success"] == True
    assert isinstance(response.json()["data"], list)
