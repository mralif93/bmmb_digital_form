#!/bin/bash
# Debug script to check QR code generation

echo "=== Checking Server Environment ==="

echo "1. Verify latest code is loaded:"
docker exec -it eform_web cat app/Console/Commands/SyncMapBranchesFromDatabase.php | grep -A 2 "reset-qr-codes"

echo ""
echo "2. Check Laravel logs for errors:"
docker exec -it eform_web tail -50 storage/logs/laravel.log

echo ""
echo "3. Check current branch count:"
docker exec -it eform_web php artisan tinker --execute="echo 'Total branches: ' . \App\Models\Branch::count() . PHP_EOL;"

echo ""
echo "4. Check QR code count:"
docker exec -it eform_web php artisan tinker --execute="echo 'Total QR codes: ' . \App\Models\QrCode::where('type', 'branch')->count() . PHP_EOL;"

echo ""
echo "5. Test QR generation directly:"
docker exec -it eform_web php artisan tinker --execute="
\$branch = \App\Models\Branch::first();
if (\$branch) {
    echo 'Testing QR generation for: ' . \$branch->branch_name . PHP_EOL;
    echo 'Branch ID: ' . \$branch->id . PHP_EOL;
    echo 'TI Agent Code: ' . \$branch->ti_agent_code . PHP_EOL;
    
    try {
        \$token = bin2hex(random_bytes(16));
        \$url = route('public.branch', ['tiAgentCode' => \$branch->ti_agent_code, 'token' => \$token]);
        echo 'QR URL: ' . \$url . PHP_EOL;
        echo 'Token: ' . \$token . PHP_EOL;
    } catch (\Exception \$e) {
        echo 'ERROR: ' . \$e->getMessage() . PHP_EOL;
    }
}
"
