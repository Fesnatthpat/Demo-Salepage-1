# คู่มือการตั้งค่าไฟล์ .env (Environment Variables) - ข้อมูลอัปเดตล่าสุด

ไฟล์ `.env` นี้ใช้สำหรับกำหนดค่าคอนฟิกูเรชันเฉพาะของโปรเจค โดยได้ตัดข้อมูลพื้นฐานออกเพื่อให้เน้นไปที่ส่วนที่สำคัญที่สุด

---

## 1. ข้อมูล CRM (Kawin Brothers)
ใช้สำหรับส่งข้อมูลคำสั่งซื้อไปยังระบบ CRM ภายนอก

| Key | คำอธิบาย | ตัวอย่างค่าคอนฟิก |
|---|---|---|
| `CRM_API_URL` | URL สำหรับ API สร้างออเดอร์ | `https://test.kawinbrothers.com/api/v1/create-order.php` |
| `CRM_API_TOKEN` | Token สำหรับยืนยันตัวตนกับ API | `cFVubW9zWUJyU...` |

---

## 2. ระบบ LINE Login (Customer Auth)
ใช้เพื่อให้ลูกค้าสามารถสมัครสมาชิกและเข้าสู่ระบบผ่าน LINE บนหน้าเว็บไซต์

| Key | คำอธิบาย | ตัวอย่างค่าคอนฟิก |
|---|---|---|
| `LINE_CLIENT_ID` | Channel ID จาก LINE Developers | `2008827364` |
| `LINE_CLIENT_SECRET` | Channel Secret | `6dff0fe5f9...` |
| `LINE_REDIRECT_URI` | URL สำหรับรับข้อมูลกลับจาก LINE | `https://your-domain.com/callback/line` |

---

## 3. ระบบ LINE Messaging API (Bot / Notification)
ใช้สำหรับให้ระบบ (Laravel) สั่งบอทในการส่งการแจ้งเตือนหรือยิงโปรโมชัน

| Key | คำอธิบาย | แหล่งที่มา |
|---|---|---|
| `LINE_BOT_CHANNEL_SECRET` | Channel Secret (รหัส 32 ตัว) | หน้า Messaging API ใน LINE Developers |
| `LINE_BOT_ACCESS_TOKEN` | Channel Access Token (รหัสยาว) | ไปที่แท็บ Messaging API แล้วกดปุ่ม Issue |

---

## 4. ระบบการชำระเงิน (Payment Settings)

| Key | คำอธิบาย | ตัวอย่างค่าคอนฟิก |
|---|---|---|
| `PROMPTPAY_ACCOUNT` | หมายเลขพร้อมเพย์สำหรับรับเงิน | `0984077060` |

---

## 5. การตั้งค่าแอปพลิเคชันและฐานข้อมูล (Basic Settings)

| Key | คำอธิบาย | ค่าเริ่มต้นที่แนะนำ |
|---|---|---|
| `APP_URL` | URL หลักของเว็บไซต์ (ต้องใส่ให้ตรงกับ Tunnel/Domain) | `https://...trycloudflare.com` |
| `APP_LOCALE` | ภาษาเริ่มต้นของระบบ | `th` |
| `DB_DATABASE` | ชื่อฐานข้อมูลที่ใช้ | `salepage_demo` |
| `SESSION_DRIVER` | การเก็บ Session | `file` หรือ `database` |
| `SESSION_SECURE_COOKIE` | เปิดใช้คุกกี้แบบปลอดภัย (สำหรับ HTTPS) | `true` |

---

## ขั้นตอนการเริ่มใช้งานใหม่
1. คัดลอกไฟล์ตัวอย่าง `cp .env.example .env`
2. ตรวจสอบ `APP_URL` ให้ตรงกับ URL ที่ใช้งานจริง (เช่น Cloudflare Tunnel)
3. ตรวจสอบ `DB_DATABASE` และ `DB_USERNAME/PASSWORD` ให้ตรงกับ MySQL ในเครื่อง
4. ใส่ `LINE_BOT_ACCESS_TOKEN` ใหม่ หากมีการกด Issue ใหม่ใน LINE Developers Console
