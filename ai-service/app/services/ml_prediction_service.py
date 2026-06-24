from app.ml.model_loader import load_crowd_model, load_model_metadata, model_exists
from app.ml.feature_builder import FeatureBuilder

class MLPredictionService:
    @staticmethod
    def predict_crowd_ml(request_data: dict) -> dict:
        if not model_exists():
            return {
                "success": False,
                "message": "ML model is not available. Please train the model first.",
                "data": None
            }
            
        model = load_crowd_model()
        metadata = load_model_metadata()
        
        if model is None:
            return {
                "success": False,
                "message": "Failed to load ML model.",
                "data": None
            }
            
        # Build features
        features_df = FeatureBuilder.build_features(request_data)
        
        # Predict
        try:
            predicted_count = model.predict(features_df)[0]
        except Exception as e:
            return {
                "success": False,
                "message": f"Prediction failed: {e}",
                "data": None
            }
            
        predicted_count = int(max(0, predicted_count))
        max_capacity = request_data.get('max_capacity', 1000)
        
        # Calculate crowd score
        if max_capacity <= 0:
            crowd_score = 0.0
        else:
            crowd_score = min(1.0, predicted_count / max_capacity)
            
        # Determine crowd level
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
            
        return {
            "success": True,
            "message": "ML crowd prediction generated successfully",
            "data": {
                "destination_id": request_data.get('destination_id'),
                "destination_name": request_data.get('destination_name'),
                "predicted_count": predicted_count,
                "max_capacity": max_capacity,
                "crowd_score": round(crowd_score, 2),
                "crowd_level": crowd_level,
                "crowd_label": crowd_label,
                "method": "machine_learning",
                "model_version": metadata.get('model_version', 'unknown') if metadata else 'unknown',
                "model_metadata": metadata or {}
            }
        }
