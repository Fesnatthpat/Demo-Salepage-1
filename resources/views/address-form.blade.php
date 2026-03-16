<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เลือกจังหวัด-อำเภอ-ตำบล</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen p-10">

    <div class="bg-white max-w-lg mx-auto p-8 rounded-xl shadow">
        <h1 class="text-2xl font-bold mb-6">กรอกที่อยู่</h1>

        <!-- จังหวัด -->
        <label class="block mb-2 font-semibold">จังหวัด</label>
        <select id="province" class="w-full p-3 border rounded mb-4">
            <option value="">-- เลือกจังหวัด --</option>
        </select>

        <!-- อำเภอ -->
        <label class="block mb-2 font-semibold">อำเภอ</label>
        <select id="amphoe" class="w-full p-3 border rounded mb-4">
            <option value="">-- เลือกอำเภอ --</option>
        </select>

        <!-- ตำบล -->
        <label class="block mb-2 font-semibold">ตำบล</label>
        <select id="tambon" class="w-full p-3 border rounded mb-4">
            <option value="">-- เลือกตำบล --</option>
        </select>

        <!-- รหัสไปรษณีย์ -->
        <label class="block mb-2 font-semibold">รหัสไปรษณีย์</label>
        <input id="zipcode" class="w-full p-3 border rounded bg-gray-100" readonly>
    </div>

<script>
const provinceSelect = document.getElementById('province');
const amphoeSelect   = document.getElementById('amphoe');
const tambonSelect   = document.getElementById('tambon');
const zipcodeInput   = document.getElementById('zipcode');

async function loadProvinces() {
    const res = await fetch('/api/address/provinces');
    const data = await res.json();

    data.forEach(item => {
        provinceSelect.innerHTML += `<option value="${item.province}">${item.province}</option>`;
    });
}

provinceSelect.addEventListener('change', async function () {
    amphoeSelect.innerHTML = `<option value="">-- เลือกอำเภอ --</option>`;
    tambonSelect.innerHTML = `<option value="">-- เลือกตำบล --</option>`;
    zipcodeInput.value = "";

    const res = await fetch(`/api/address/amphoes?province=${this.value}`);
    const data = await res.json();

    data.forEach(item => {
        amphoeSelect.innerHTML += `<option value="${item.amphoe}">${item.amphoe}</option>`;
    });
});

amphoeSelect.addEventListener('change', async function () {
    tambonSelect.innerHTML = `<option value="">-- เลือกตำบล --</option>`;
    zipcodeInput.value = "";

    const res = await fetch(`/api/address/tambons?province=${provinceSelect.value}&amphoe=${this.value}`);
    const data = await res.json();

    data.forEach(item => {
        tambonSelect.innerHTML += `<option value="${item.tambon}">${item.tambon}</option>`;
    });
});

tambonSelect.addEventListener('change', async function () {
    const res = await fetch(`/api/address/zipcodes?province=${provinceSelect.value}&amphoe=${amphoeSelect.value}&tambon=${this.value}`);
    const data = await res.json();

    zipcodeInput.value = data[0]?.zipcode ?? "";
});

// โหลดจังหวัดตอนเปิดหน้า
loadProvinces();
</script>

</body>
</html>
