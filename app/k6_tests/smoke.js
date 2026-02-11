// app/k6_tests/smoke.js
import http from 'k6/http';
import { sleep } from 'k6';

const BASE = 'http://127.0.0.1:8000';
const TOKEN = '1|qRNPKI6eCle0rZUrWM6ij8tsCqJxVzJ7y2RrRWas'; // pega tu token Sanctum

export const options = {
  vus: 10,
  duration: '30s',
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<800'],
  },
};

export default function () {
  const headers = { headers: { Authorization: `Bearer ${TOKEN}` } };
  http.get(`${BASE}/api/minibar-products`, headers);
  sleep(1);
}
