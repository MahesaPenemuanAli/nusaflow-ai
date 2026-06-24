from fastapi import APIRouter
from app.schemas.ml_prediction_schema import MLCrowdPredictionRequest
from app.services.ml_prediction_service import MLPredictionService
from app.ml.model_loader import model_exists, load_model_metadata

router = APIRouter()

@router.get("/model-info", tags=["Machine Learning"])
def get_model_info():
    is_available = model_exists()
    metadata = load_model_metadata() if is_available else None
    
    return {
        "success": True,
        "message": "Model information retrieved successfully",
        "data": {
            "model_available": is_available,
            "model_version": metadata.get("model_version") if metadata else None,
            "metadata": metadata or {}
        }
    }

@router.post("/predict-crowd-ml", tags=["Machine Learning"])
def predict_crowd_ml(request: MLCrowdPredictionRequest):
    request_data = request.model_dump()
    response = MLPredictionService.predict_crowd_ml(request_data)
    return response
