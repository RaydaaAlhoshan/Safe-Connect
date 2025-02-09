document.getElementById('agree-checkbox').addEventListener('change', function () {
    document.getElementById('confirm-button').disabled = !this.checked;
});

// حساب المدة الزمنية 24 ساعة
const countdownDuration = 24 * 60 * 60 * 1000; // 24 ساعة بالمللي ثانية
const countdownElement = document.getElementById('countdown-timer');
countdownElement.style.color='red';
const endTime = Date.now() + countdownDuration; // وقت انتهاء العداد

// تحديث العداد كل ثانية
const countdownInterval = setInterval(function () {
    const now = Date.now();
    const timeLeft = endTime - now;

    if (timeLeft <= 0) {
        clearInterval(countdownInterval);
        declinePlan();
        countdownElement.textContent = "Time's up! Plan declined.";
    } else {
        const hours = Math.floor((timeLeft / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((timeLeft / (1000 * 60)) % 60);
        const seconds = Math.floor((timeLeft / 1000) % 60);
        countdownElement.textContent = `${hours}h ${minutes}m ${seconds}s`;
    }
}, 1000);

// عند انتهاء الوقت، يتم رفض الخطة تلقائيًا
function declinePlan() {
    const planId = sessionStorage.getItem('currentPlanId');

    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            planId: planId,
            status: 'declined'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("The contract was not signed within 24 hours. Plan declined automatically.");
            window.location.href = 'specialistHomePage.php';
        }
    });
}
