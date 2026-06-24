from pydantic import BaseModel, Field
from typing import Optional

class CrowdPredictionRequest(BaseModel):
    destination_id: int
    destination_name: str
    max_capacity: int = Field(..., ge=0)
    visitor_count: int = Field(..., ge=0)
    prediction_date: str
    prediction_hour: Optional[int] = Field(None, ge=0, le=23)
    is_weekend: bool
    has_event: bool
    event_impact: Optional[str] = None # 'low', 'medium', 'high', or null
    weather: Optional[str] = None

class CrowdPredictionResponse(BaseModel):
    destination_id: int
    destination_name: str
    prediction_date: str
    prediction_hour: Optional[int]
    visitor_count: int
    max_capacity: int
    crowd_score: float
    crowd_level: str
    crowd_label: str
    method: str
    confidence_score: float
    factors: dict
