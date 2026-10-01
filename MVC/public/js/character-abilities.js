document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.ability-update-form');
    if (!form) return;

    const inputs = Array.from(form.querySelectorAll('.ability-score-field input'));
    const pointsLeft = form.querySelector('.ability-points-left');
    const maxPoints = Number(form.dataset.maxPoints);
    const hpMax = Number(form.dataset.hpMax);

    function updatePointBudget() {
        const used = hpMax + inputs.reduce((total, input) => total + (Number(input.value) || 0), 0);
        const remaining = maxPoints - used;
        pointsLeft.textContent = String(remaining);
        pointsLeft.parentElement.classList.toggle('form-error', remaining < 0);
        return remaining;
    }

    function clampInput(input) {
        const otherScores = inputs.reduce(function (total, candidate) {
            return candidate === input ? total : total + (Number(candidate.value) || 0);
        }, 0);
        const allowedMax = Math.min(25, maxPoints - hpMax - otherScores);
        const requestedValue = Number(input.value);

        input.value = String(Math.max(1, Math.min(
            Number.isFinite(requestedValue) ? requestedValue : 1,
            allowedMax
        )));
        updatePointBudget();
    }

    inputs.forEach(function (input) {
        input.addEventListener('input', function () {
            clampInput(input);
        });
        input.addEventListener('change', function () {
            clampInput(input);
        });
    });

    form.addEventListener('submit', function (event) {
        if (updatePointBudget() < 0) {
            event.preventDefault();
        }
    });

    updatePointBudget();
});
