</html>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز المواعيد</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .time-slot {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
            position: relative;
        }
        .booked {
            background-color: #f0f0f0;
            color: #888;
            cursor: not-allowed;
        }
        .booked::after {
            content: '✖';
            color: red;
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 16px;
        }
        #bookButton {
            background-color: #4CAF50;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>حجز المواعيد</h1>

    <h2>الخميس 27 مارس</h2>
    <div id="day1"></div>

    <h2>الجمعة 28 مارس</h2>
    <div id="day2"></div>

    <h2>السبت 29 مارس</h2>
    <div id="day3"></div>

    <button id="bookButton" disabled onclick="validateAndSubmit()">حجز الموعد</button>

    <script>
        // استخراج بيانات الاسم ورقم الهاتف من الرابط
        const urlParams = new URLSearchParams(window.location.search);
        const fullName = urlParams.get('fullName');
        const phoneNumber = urlParams.get('phoneNumber');

        const day1 = document.getElementById('day1');
        const day2 = document.getElementById('day2');
        const day3 = document.getElementById('day3');
        const bookButton = document.getElementById('bookButton');
        let selectedTime = null;
        let bookedTimes = JSON.parse(localStorage.getItem('bookedTimes')) || [];

        function generateTimeSlots(container, startTime, endTime, intervalMinutes) {
            let currentDate = new Date('2024-03-27T00:00:00');
            let currentTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), parseInt(startTime.split(':')[0]), parseInt(startTime.split(':')[1]));
            let end = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), parseInt(endTime.split(':')[0]), parseInt(endTime.split(':')[1]));

            // تعديل التاريخ ليوم الجمعة والسبت
            if (container.id === 'day2') {
                currentTime.setDate(28);
                end.setDate(28);
                if (parseInt(endTime.split(':')[0]) < parseInt(startTime.split(':')[0])) {
                    end.setDate(29); // إذا كان وقت النهاية في اليوم التالي
                }
            } else if (container.id === 'day3') {
                currentTime.setDate(29);
                end.setDate(29);
                if (parseInt(endTime.split(':')[0]) < parseInt(startTime.split(':')[0])) {
                    end.setDate(30); // إذا كان وقت النهاية في اليوم التالي
                }
            }

            while (currentTime < end) {
                const timeString = currentTime.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });

                // استثناء موعد الساعة 7:00 مساءً (19:00) فقط
                if (currentTime.getHours() === 19 && currentTime.getMinutes() === 0) {
                    currentTime.setMinutes(currentTime.getMinutes() + intervalMinutes);
                    continue; // تخطي هذا الموعد
                }

                const timeSlot = document.createElement('div');
                timeSlot.classList.add('time-slot');
                timeSlot.textContent = timeString;

                if (bookedTimes.includes(timeString + container.id)) {
                    timeSlot.classList.add('booked');
                } else {
                    timeSlot.addEventListener('click', () => {
                        if (selectedTime) {
                            selectedTime.style.backgroundColor = '';
                        }
                        selectedTime = timeSlot;
                        timeSlot.style.backgroundColor = '#e0f7fa';
                        bookButton.disabled = false;
                    });
                }
                container.appendChild(timeSlot);
                currentTime.setMinutes(currentTime.getMinutes() + intervalMinutes);
            }
        }

        // إنشاء مواعيد الخميس
        generateTimeSlots(day1, '08:00', '24:00', 20);

        // إنشاء مواعيد الجمعة
        generateTimeSlots(day2, '08:00', '04:00', 20);

        // إنشاء مواعيد السبت
        generateTimeSlots(day3, '08:00', '06:00', 20); // تعديل وقت النهاية إلى 6 صباحًا (06:00)

        function validateAndSubmit() {
            if (!selectedTime) {
                alert('الرجاء اختيار موعد.');
                return;
            }

            // إذا تم اختيار موعد، قم بإرسال النموذج
            redirectToContact();
        }

        function redirectToContact() {
            if (selectedTime) {
                const timeString = selectedTime.textContent;
                const containerId = selectedTime.parentElement.id;
                const date = containerId === 'day1' ? '27 مارس' : containerId === 'day2' ? '2024-03-28' : '2024-03-29';
                bookedTimes.push(timeString + containerId);
                localStorage.setItem('bookedTimes', JSON.stringify(bookedTimes));
                selectedTime.classList.add('booked');
                selectedTime.style.backgroundColor = '';
                selectedTime = null;
                bookButton.disabled = true;
                alert(` حجز الموعد بنجاح في تم${date} الساعة ${timeString}`);

                // إرسال البيانات إلى contact.php باستخدام نموذج
                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'contact.php';

                const fullNameInput = document.createElement('input');
                fullNameInput.type = 'hidden';
                fullNameInput.name = 'fullName';
                fullNameInput.value = fullName;
                form.appendChild(fullNameInput);

                const phoneNumberInput = document.createElement('input');
                phoneNumberInput.type = 'hidden';
                phoneNumberInput.name = 'phoneNumber';
                phoneNumberInput.value = phoneNumber;
                form.appendChild(phoneNumberInput);

                const dateInput = document.createElement('input');
                dateInput.type = 'hidden';
                dateInput.name = 'date';
                dateInput.value = containerId === 'day1' ? '2024-03-27' : containerId === 'day2' ? '2024-03-28' : '2024-03-29';
                form.appendChild(dateInput);

                const timeInput = document.createElement('input');
                timeInput.type = 'hidden';
                timeInput.name = 'time';
                timeInput.value = timeString;
                form.appendChild(timeInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
        async function loadBookedTimes() {
    try {
        const response = await fetch('get_booked_times.php');
        const data = await response.json();
        bookedTimes = data.map(item => item.time + item.date); // تخزينها في مصفوفة
        localStorage.setItem('bookedTimes', JSON.stringify(bookedTimes)); // تخزينها مؤقتًا
    } catch (error) {
        console.error('خطأ في تحميل المواعيد:', error);
    }
}

// استدعاء الدالة عند تحميل الصفحة
loadBookedTimes();

    </script>
</body>
</html>

