Jalankan flaskAPI
uvicorn main:app --reload --port 8001

Jalankan Laravel
php artisan serve

Train Curl
cara 1:
curl -v -X POST "http://127.0.0.1:8001/internal/train" -H "Content-Type: application/json" -H "X-INTERNAL-TOKEN: HAIKYU2025" -d "{\"dataset_id\":3,\"epochs\":2,\"max_words\":20000,\"maxlen\":100,\"model_name_prefix\":\"admin_run\"}"

cara 2:
curl -v -X POST "http://127.0.0.1:8001/internal/train" ^
  -H "Content-Type: application/json" ^
  -H "X-INTERNAL-TOKEN: HAIKYU2025" ^
  -d "{\"dataset_id\":3,\"epochs\":2,\"max_words\":20000,\"maxlen\":100,\"model_name_prefix\":\"admin_run\"}"
