// Character Creator - Real-time summary panel update
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const charNameInput = document.querySelector('input[name="character_name"]');
    const classRadios = document.querySelectorAll('input[name="character_class_id"]');
    const raceRadios = document.querySelectorAll('input[name="character_race_id"]');
    const jobSelect = document.querySelector('select[name="character_job_id"]');
    const levelInput = document.querySelector('input[name="level"]');
    const hpInput = document.querySelector('input[name="hp_max"]');
    const statInputs = document.querySelectorAll('.stat-input');
    const pointsLeftEl = document.getElementById('pointsLeft');
    const MaxPoints = 47;

    function getUsedPoints() {
        let used = 0;
        statInputs.forEach(input => {
            used += Number(input.value) || 0;
        });
        return used;
    }

    function updatePointsDisplay() {
        const totalUsed = getUsedPoints();
        const remaining = Math.max(MaxPoints - totalUsed, 0);
        if (pointsLeftEl) {
            pointsLeftEl.textContent = remaining;
        }
        if (pointsLeftEl && pointsLeftEl.parentElement) {
            pointsLeftEl.parentElement.style.borderColor = remaining === 0 ? 'rgba(213, 169, 30, 0.75)' : 'rgba(213, 169, 30, 0.3)';
        }

        document.querySelectorAll('.stat-button').forEach(button => {
            const targetName = button.dataset.target || button.dataset.stat;
            const input = document.querySelector(`input[name="${targetName}"]`);
            if (!input) return;

            const currentValue = Number(input.value) || 0;
            const min = Number(input.min) || 0;
            const max = Number(input.max) || MaxPoints;

            if (button.dataset.action === 'increase') {
                button.disabled = currentValue >= max || getUsedPoints() >= MaxPoints;
            } else {
                button.disabled = currentValue <= min;
            }
        });
    }

    function clampStatValue(input) {
        const min = Number(input.min) || 0;
        const max = Number(input.max) || MaxPoints;
        let value = Number(input.value) || 0;
        value = Math.min(Math.max(value, min), max);
        input.value = value;
    }

    function updateSummary() {
        const charName = charNameInput ? (charNameInput.value || 'Unnamed') : 'Unnamed';
        document.getElementById('summaryName').textContent = charName;

        const selectedClass = document.querySelector('input[name="character_class_id"]:checked');
        const className = selectedClass ? selectedClass.getAttribute('data-class') : '—';
        document.getElementById('statClass').textContent = className;

        const selectedRace = document.querySelector('input[name="character_race_id"]:checked');
        const raceName = selectedRace ? selectedRace.getAttribute('data-race') : '—';
        document.getElementById('statRace').textContent = raceName;

        const subtitle = selectedClass ? `${raceName} ${className}` : 'No class/race selected';
        document.getElementById('summarySubtitle').textContent = subtitle;

        const selectedJob = jobSelect && jobSelect.options[jobSelect.selectedIndex];
        const jobName = selectedJob ? selectedJob.getAttribute('data-job') || '—' : '—';
        document.getElementById('statJob').textContent = jobName;

        document.getElementById('statLevel').textContent = levelInput && levelInput.value ? levelInput.value : 1;
        document.getElementById('statHP').textContent = hpInput && hpInput.value ? hpInput.value : 10;

        const statIds = {
            agi: 'statAGI',
            str: 'statSTR',
            dex: 'statDEX',
            wis: 'statWIS',
            cha: 'statCHA',
            con: 'statCON',
            int: 'statINT'
        };

        Object.entries(statIds).forEach(([statName, summaryId]) => {
            const input = document.querySelector(`input[name="${statName}"]`);
            const target = document.getElementById(summaryId);
            if (target) {
                target.textContent = input && input.value ? input.value : 0;
            }
        });

        classRadios.forEach(radio => {
            const card = radio.parentElement;
            if (card) card.classList.toggle('selected', radio.checked);
        });

        raceRadios.forEach(radio => {
            const card = radio.parentElement;
            if (card) card.classList.toggle('selected', radio.checked);
        });
    }

    statInputs.forEach(input => {
        input.addEventListener('input', function() {
            clampStatValue(this);
            updatePointsDisplay();
            updateSummary();
        });
        input.addEventListener('change', function() {
            clampStatValue(this);
            updatePointsDisplay();
            updateSummary();
        });
    });

    document.querySelectorAll('.stat-button').forEach(button => {
        button.addEventListener('click', function() {
            const targetName = this.dataset.target || this.dataset.stat;
            const action = this.dataset.action;
            const input = document.querySelector(`input[name="${targetName}"]`);
            if (!input) return;

            const currentValue = Number(input.value) || 0;
            const min = Number(input.min) || 0;
            const max = Number(input.max) || MaxPoints;


            if (action === 'decrease') {
                if (currentValue <= min) return;
                input.value = currentValue - 1;
                updatePointsDisplay();
                updateSummary();
                return;
            }

            if (action === 'increase') {
                if (currentValue >= max) return;
                if (getUsedPoints() >= MaxPoints) return;
                input.value = currentValue + 1;
                updatePointsDisplay();
                updateSummary();
            }
        });
    });

    if (hpInput) {
        hpInput.addEventListener('input', updateSummary);
        hpInput.addEventListener('change', updateSummary);
    }

    if (charNameInput) charNameInput.addEventListener('input', updateSummary);
    if (classRadios.length) classRadios.forEach(radio => radio.addEventListener('change', updateSummary));
    if (raceRadios.length) raceRadios.forEach(radio => radio.addEventListener('change', updateSummary));
    if (jobSelect) jobSelect.addEventListener('change', updateSummary);
    if (levelInput) levelInput.addEventListener('input', updateSummary);

    updatePointsDisplay();
    updateSummary();
});
