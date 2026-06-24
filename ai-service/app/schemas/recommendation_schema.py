from pydantic import BaseModel
from typing import Optional, List

class DestinationCandidate(BaseModel):
    destination_id: int
    name: str
    category: str
    max_capacity: int
    visitor_count: int
    rating: Optional[float] = None
    distance_km: Optional[float] = None
    is_active: bool

class RecommendationRequest(BaseModel):
    current_destination_id: int
    preferred_category: Optional[str] = None
    avoid_crowded: bool = True
    limit: int = 5
    candidates: List[DestinationCandidate]

class RecommendationResponse(BaseModel):
    destination_id: int
    name: str
    category: str
    recommendation_score: float
    crowd_score: float
    crowd_level: str
    crowd_label: str
    reason: str
