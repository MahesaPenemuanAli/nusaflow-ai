from fastapi import APIRouter
from app.schemas.recommendation_schema import RecommendationRequest
from app.services.recommendation_service import RecommendationService

router = APIRouter()

@router.post("/recommend-destinations", tags=["Recommendation"])
def recommend_destinations(request: RecommendationRequest):
    recommendations = RecommendationService.recommend_destinations(request)
    return {
        "success": True,
        "message": "Destination recommendations generated successfully",
        "data": recommendations
    }
