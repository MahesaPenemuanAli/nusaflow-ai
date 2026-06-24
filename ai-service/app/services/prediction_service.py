from app.schemas.prediction_schema import CrowdPredictionRequest

class PredictionService:
    @staticmethod
    def predict_crowd(request: CrowdPredictionRequest) -> dict:
        if request.max_capacity <= 0:
            crowd_score = 0.0
        else:
            base_score = request.visitor_count / request.max_capacity
            
            # Adjustments
            if request.is_weekend:
                base_score += 0.10
            
            if request.has_event and request.event_impact:
                impact = request.event_impact.lower()
                if impact == 'low':
                    base_score += 0.05
                elif impact == 'medium':
                    base_score += 0.10
                elif impact == 'high':
                    base_score += 0.20
            
            if request.weather:
                weather = request.weather.lower()
                if weather in ['hujan', 'rainy', 'rain']:
                    base_score -= 0.05
                elif weather in ['cerah', 'clear', 'sunny']:
                    base_score += 0.03
            
            crowd_score = max(0.0, min(1.0, base_score))
        
        # Determine level and label
        if crowd_score <= 0.30:
            crowd_level = "low"
            crowd_label = "Sepi"
        elif crowd_score <= 0.60:
            crowd_level = "moderate"
            crowd_label = "Normal"
        elif crowd_score <= 0.85:
            crowd_level = "high"
            crowd_label = "Ramai"
        else:
            crowd_level = "packed"
            crowd_label = "Sangat Ramai"
            
        factors = {
            "is_weekend": request.is_weekend,
            "has_event": request.has_event,
            "event_impact": request.event_impact,
            "weather": request.weather
        }
        
        return {
            "destination_id": request.destination_id,
            "destination_name": request.destination_name,
            "prediction_date": request.prediction_date,
            "prediction_hour": request.prediction_hour,
            "visitor_count": request.visitor_count,
            "max_capacity": request.max_capacity,
            "crowd_score": round(crowd_score, 2),
            "crowd_level": crowd_level,
            "crowd_label": crowd_label,
            "method": "rule_based_fastapi",
            "confidence_score": 0.70,
            "factors": factors
        }
