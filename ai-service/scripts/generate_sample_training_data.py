import os
import random
import pandas as pd
from datetime import datetime, timedelta

def generate_data():
    num_samples = 500
    data = []
    
    categories = ['Pantai', 'Pegunungan', 'Museum', 'Taman Hiburan', 'Kuliner']
    weathers = ['clear', 'cloudy', 'rainy', 'storm']
    event_impacts = ['none', 'low', 'medium', 'high']
    
    for _ in range(num_samples):
        destination_id = random.randint(1, 10)
        category = random.choice(categories)
        
        # Max capacity depends somewhat on category
        if category == 'Pantai':
            max_capacity = random.randint(1000, 5000)
        elif category == 'Museum':
            max_capacity = random.randint(300, 1000)
        else:
            max_capacity = random.randint(500, 3000)
            
        month = random.randint(1, 12)
        day_of_week = random.randint(0, 6)
        hour = random.randint(0, 23)
        
        is_weekend = day_of_week >= 5
        is_holiday = random.random() < 0.1
        
        has_event = random.random() < 0.2
        event_impact = random.choice(event_impacts) if has_event else 'none'
        
        weather = random.choice(weathers)
        temperature = round(random.uniform(22.0, 35.0), 1)
        rain_level = round(random.uniform(0.0, 50.0), 1) if weather in ['rainy', 'storm'] else 0.0
        
        # Generate a realistic visitor count logic
        base_visitors = max_capacity * 0.1
        
        # Hour factor
        if 9 <= hour <= 15:
            base_visitors *= 2.5
        elif 16 <= hour <= 19:
            base_visitors *= 1.5
        elif hour < 6 or hour > 21:
            base_visitors *= 0.1
            
        # Weekend / Holiday factor
        if is_weekend:
            base_visitors *= 1.5
        if is_holiday:
            base_visitors *= 1.8
            
        # Weather factor
        if weather == 'rainy':
            base_visitors *= 0.6
        elif weather == 'storm':
            base_visitors *= 0.2
        elif weather == 'clear':
            base_visitors *= 1.2
            
        # Event factor
        if has_event:
            if event_impact == 'high':
                base_visitors *= 1.5
            elif event_impact == 'medium':
                base_visitors *= 1.2
            elif event_impact == 'low':
                base_visitors *= 1.1
                
        # Random noise
        visitor_count = int(base_visitors * random.uniform(0.8, 1.2))
        visitor_count = min(visitor_count, max_capacity)
        visitor_count = max(0, visitor_count)
        
        data.append({
            'destination_id': destination_id,
            'category': category,
            'max_capacity': max_capacity,
            'day_of_week': day_of_week,
            'month': month,
            'hour': hour,
            'is_weekend': is_weekend,
            'is_holiday': is_holiday,
            'has_event': has_event,
            'event_impact': event_impact,
            'weather': weather,
            'temperature': temperature,
            'rain_level': rain_level,
            'visitor_count': visitor_count
        })
        
    df = pd.DataFrame(data)
    
    # Ensure directory exists
    os.makedirs('data/training', exist_ok=True)
    df.to_csv('data/training/visitor_training_sample.csv', index=False)
    print(f"Successfully generated {num_samples} sample data to data/training/visitor_training_sample.csv")

if __name__ == "__main__":
    generate_data()
