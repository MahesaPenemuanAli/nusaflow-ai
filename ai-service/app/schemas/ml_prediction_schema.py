from pydantic import BaseModel, Field
from typing import Optional

class MLCrowdPredictionRequest(BaseModel):
    destination_id: int
    destination_name: str
    category: str
    max_capacity: int = Field(..., ge=0)
    day_of_week: int = Field(..., ge=0, le=6)
    month: int = Field(..., ge=1, le=12)
    hour: int = Field(..., ge=0, le=23)
    is_weekend: bool
    is_holiday: bool
    has_event: bool
    event_impact: Optional[str] = None # 'low', 'medium', 'high', 'none', or null
    weather: Optional[str] = None # 'clear', 'cloudy', 'rainy', 'storm', or null
    temperature: Optional[float] = None
    rain_level: Optional[float] = None
