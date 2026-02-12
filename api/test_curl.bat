@echo off
echo Testing verify-email endpoint via curl...
echo.

curl -X POST "http://localhost/rappel/api/auth/verify-email" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"test@example.com\",\"code\":\"123456\"}" ^
  -w "\n\nHTTP Status: %%{http_code}\n" ^
  -v

echo.
echo.
echo === Test Complete ===
pause
