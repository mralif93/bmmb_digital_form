@extends('layouts.admin-minimal')

@section('title', 'Branch QR Code Generator - BMMB Digital Forms')
@section('page-title', 'Branch QR Code Generator')
@section('page-description', 'Generate QR codes for different branches')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Branch QR Generator -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Generate Branch QR Codes</h3>
        
        <form id="branchQrForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="branch_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Branch Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="branch_name" name="branch_name" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., Kuala Lumpur Branch">
                </div>
                
                <div>
                    <label for="branch_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Branch Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="branch_code" name="branch_code" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., KL001">
                </div>
                
                <div>
                    <label for="branch_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Branch Address
                    </label>
                    <textarea id="branch_address" name="branch_address" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Enter branch address"></textarea>
                </div>
                
                <div>
                    <label for="branch_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Branch Phone
                    </label>
                    <input type="tel" id="branch_phone" name="branch_phone"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., +60 3-1234 5678">
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors">
                    Generate QR Code
                </button>
            </div>
        </form>
    </div>

    <!-- QR Code Display -->
    <div id="qrResult" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hidden">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Generated QR Code</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- QR Code Image -->
            <div class="text-center">
                <div id="qrCodeContainer" class="inline-block p-4 bg-white rounded-lg shadow-md">
                    <!-- QR code will be inserted here -->
                </div>
                <div class="mt-4">
                    <button onclick="downloadQrCode()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class='bx bx-download mr-2'></i>
                        Download QR Code
                    </button>
                </div>
            </div>
            
            <!-- Branch Information -->
            <div>
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Branch Information</h4>
                <div id="branchInfo" class="space-y-2 text-sm">
                    <!-- Branch info will be inserted here -->
                </div>
                
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">QR Code URL:</h5>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="qrUrl" readonly
                               class="flex-1 px-2 py-1 text-xs bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded text-gray-600 dark:text-gray-300">
                        <button onclick="copyUrl()" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors">
                            Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Branches -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sample Branches</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Click on any sample branch to generate its QR code:</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div onclick="fillSampleBranch('KL001', 'Kuala Lumpur Branch', 'Jalan Sultan Ismail, Kuala Lumpur', '+60 3-1234 5678')" 
                 class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 cursor-pointer transition-colors">
                <h4 class="font-semibold text-gray-900 dark:text-white">Kuala Lumpur Branch</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Code: KL001</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Jalan Sultan Ismail, KL</p>
            </div>
            
            <div onclick="fillSampleBranch('JB002', 'Johor Bahru Branch', 'Jalan Tebrau, Johor Bahru', '+60 7-2345 6789')" 
                 class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 cursor-pointer transition-colors">
                <h4 class="font-semibold text-gray-900 dark:text-white">Johor Bahru Branch</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Code: JB002</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Jalan Tebrau, JB</p>
            </div>
            
            <div onclick="fillSampleBranch('PG003', 'Penang Branch', 'Jalan Macalister, Georgetown', '+60 4-3456 7890')" 
                 class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 cursor-pointer transition-colors">
                <h4 class="font-semibold text-gray-900 dark:text-white">Penang Branch</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Code: PG003</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Jalan Macalister, Georgetown</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentQrCodeData = null;

document.getElementById('branchQrForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const branchData = {
        name: formData.get('branch_name'),
        code: formData.get('branch_code'),
        address: formData.get('branch_address'),
        phone: formData.get('branch_phone')
    };
    
    generateBranchQr(branchData);
});

function generateBranchQr(branchData) {
    // Create branch URL (you can customize this)
    const branchUrl = `${window.location.origin}/branch/${branchData.code}`;
    
    // Create QR code using a simple API (you can replace this with your preferred QR generator)
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(branchUrl)}`;
    
    // Display QR code
    document.getElementById('qrCodeContainer').innerHTML = `
        <img src="${qrCodeUrl}" alt="QR Code for ${branchData.name}" class="w-64 h-64">
    `;
    
    // Display branch information
    document.getElementById('branchInfo').innerHTML = `
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Name:</span>
            <span class="font-medium text-gray-900 dark:text-white">${branchData.name}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Code:</span>
            <span class="font-medium text-gray-900 dark:text-white">${branchData.code}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Address:</span>
            <span class="font-medium text-gray-900 dark:text-white">${branchData.address || 'Not provided'}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Phone:</span>
            <span class="font-medium text-gray-900 dark:text-white">${branchData.phone || 'Not provided'}</span>
        </div>
    `;
    
    // Set URL
    document.getElementById('qrUrl').value = branchUrl;
    
    // Store current QR data
    currentQrCodeData = {
        url: qrCodeUrl,
        branchUrl: branchUrl,
        branchData: branchData
    };
    
    // Show result
    document.getElementById('qrResult').classList.remove('hidden');
    
    // Scroll to result
    document.getElementById('qrResult').scrollIntoView({ behavior: 'smooth' });
}

function fillSampleBranch(code, name, address, phone) {
    document.getElementById('branch_code').value = code;
    document.getElementById('branch_name').value = name;
    document.getElementById('branch_address').value = address;
    document.getElementById('branch_phone').value = phone;
}

function downloadQrCode() {
    if (!currentQrCodeData) return;
    
    const link = document.createElement('a');
    link.href = currentQrCodeData.url;
    link.download = `qr-code-${currentQrCodeData.branchData.code}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function copyUrl() {
    const urlInput = document.getElementById('qrUrl');
    urlInput.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copied!';
    button.classList.add('bg-green-600', 'hover:bg-green-700');
    button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600', 'hover:bg-green-700');
        button.classList.add('bg-blue-600', 'hover:bg-blue-700');
    }, 2000);
}
</script>
@endsection


