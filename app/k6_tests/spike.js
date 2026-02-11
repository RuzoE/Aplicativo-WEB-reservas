// app/k6_tests/spike.js
import http from 'k6/http';

const BASE = 'http://127.0.0.1:8000';
const TOKEN = '1|qRNPKI6eCle0rZUrWM6ij8tsCqJxVzJ7y2RrRWas';

export const options = {
  stages: [
    { duration: '10s', target: 1 },
    { duration: '10s', target: 30 },
    { duration: '15s', target: 5 },
  ],
  thresholds: {
    http_req_failed: ['rate<0.02'],
    http_req_duration: ['p(95)<900'],
  },
};

export default function () {
  const headers = { headers: { Authorization: `Bearer ${TOKEN}` } };
  http.get(`${BASE}/api/minibar-products`, headers);
}
