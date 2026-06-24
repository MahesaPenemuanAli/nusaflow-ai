import os
import json
import joblib
import pandas as pd
from datetime import datetime
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.compose import ColumnTransformer
from sklearn.preprocessing import OneHotEncoder
from sklearn.pipeline import Pipeline
from sklearn.metrics import mean_absolute_error, root_mean_squared_error, r2_score

def train_model():
    print("Loading data...")
    data_path = 'data/training/visitor_training_sample.csv'
    
    if not os.path.exists(data_path):
        print(f"Error: {data_path} not found. Run generate_sample_training_data.py first.")
        return
        
    df = pd.read_csv(data_path)
    
    # Target
    y = df['visitor_count']
    
    # Features
    X = df.drop(columns=['visitor_count'])
    
    # Split
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    
    # Preprocessing
    categorical_features = ['category', 'event_impact', 'weather']
    
    preprocessor = ColumnTransformer(
        transformers=[
            ('cat', OneHotEncoder(handle_unknown='ignore'), categorical_features)
        ],
        remainder='passthrough'
    )
    
    # Pipeline
    model = Pipeline(steps=[
        ('preprocessor', preprocessor),
        ('regressor', RandomForestRegressor(n_estimators=100, random_state=42))
    ])
    
    print("Training model...")
    model.fit(X_train, y_train)
    
    print("Evaluating model...")
    y_pred = model.predict(X_test)
    
    mae = mean_absolute_error(y_test, y_pred)
    rmse = root_mean_squared_error(y_test, y_pred)
    r2 = r2_score(y_test, y_pred)
    
    print(f"MAE: {mae:.2f}")
    print(f"RMSE: {rmse:.2f}")
    print(f"R2 Score: {r2:.2f}")
    
    # Save model
    os.makedirs('saved_models', exist_ok=True)
    model_path = 'saved_models/crowd_prediction_model.joblib'
    joblib.dump(model, model_path)
    print(f"Model saved to {model_path}")
    
    # Save metadata
    metadata = {
        "model_name": "RandomForestRegressor",
        "model_version": "ml-rf-v1",
        "target": "visitor_count",
        "metrics": {
            "mae": round(float(mae), 2),
            "rmse": round(float(rmse), 2),
            "r2": round(float(r2), 2)
        },
        "trained_at": datetime.now().isoformat()
    }
    
    metadata_path = 'saved_models/model_metadata.json'
    with open(metadata_path, 'w') as f:
        json.dump(metadata, f, indent=4)
        
    print(f"Metadata saved to {metadata_path}")

if __name__ == "__main__":
    train_model()
