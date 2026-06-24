from fastapi import APIRouter
from app.schemas.prediction_schema import CrowdPredictionRequest
from app.services.prediction_service import PredictionService

router = APIRouter()

@router.post("/predict-crowd", tags=["Prediction"])
def predict_crowd(request: CrowdPredictionRequest):
    prediction_data = PredictionService.predict_crowd(request)
    return {
        "success": True,
        "message": "Crowd prediction generated successfully",
        "data": prediction_data
    }
