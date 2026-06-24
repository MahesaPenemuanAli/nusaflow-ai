import pandas as pd

class FeatureBuilder:
    @staticmethod
    def build_features(request_data: dict) -> pd.DataFrame:
        """
        Convert raw dict from request into a pandas DataFrame suitable for the model pipeline.
        The pipeline in train_crowd_model.py will handle the OneHotEncoding and scaling.
        """
        # Ensure 'none' string is used if event_impact is None or empty
        event_impact = request_data.get('event_impact')
        if not event_impact:
            event_impact = 'none'
            
        weather = request_data.get('weather')
        if not weather:
            weather = 'clear'
            
        data = {
            'destination_id': [request_data.get('destination_id', 0)],
            'category': [request_data.get('category', 'Unknown')],
            'max_capacity': [request_data.get('max_capacity', 1000)],
            'day_of_week': [request_data.get('day_of_week', 0)],
            'month': [request_data.get('month', 1)],
            'hour': [request_data.get('hour', 12)],
            'is_weekend': [request_data.get('is_weekend', False)],
            'is_holiday': [request_data.get('is_holiday', False)],
            'has_event': [request_data.get('has_event', False)],
            'event_impact': [event_impact],
            'weather': [weather],
            'temperature': [request_data.get('temperature', 28.0) or 28.0],
            'rain_level': [request_data.get('rain_level', 0.0) or 0.0]
        }
        
        return pd.DataFrame(data)
