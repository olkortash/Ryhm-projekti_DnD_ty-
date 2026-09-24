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
            pointsLeftEl.parentElement.style.borderColor =
                remaining === 0
                    ? 'rgba(213, 169, 30, 0.75)'
                    : 'rgba(213, 169, 30, 0.3)';
        }

        document.querySelectorAll('.stat-button').forEach(button => {
            const targetName = button.dataset.target || button.dataset.stat;
            const input = document.querySelector(`input[name="${targetName}"]`);

            if (!input) return;

            const currentValue = Number(input.value) || 0;
            const min = Number(input.min) || 0;
            const max = Number(input.max) || MaxPoints;
            const totalUsed = getUsedPoints();

            if (button.dataset.action === 'increase') {
                button.disabled =
                    currentValue >= max ||
                    totalUsed >= MaxPoints;
            }

            if (button.dataset.action === 'decrease') {
                button.disabled = currentValue <= min;
            }
        });
    }

    function setStatValue(input, requestedValue) {
        const min = Number(input.min) || 0;
        const max = Number(input.max) || MaxPoints;
        const oldValue = Number(input.value) || 0;

        let newValue = Number(requestedValue);

        if (!Number.isFinite(newValue)) {
            newValue = oldValue;
        }

        newValue = Math.min(
            Math.max(newValue, min),
            max
        );

        const otherTotal = getUsedPoints() - oldValue;

        const allowedMax = Math.min(
            max,
            MaxPoints - otherTotal
        );

        newValue = Math.min(newValue, allowedMax);

        if (newValue < min) {
            newValue = min;
        }

        input.value = newValue;
        input.dataset.previousValue = String(newValue);

        updatePointsDisplay();
        updateSummary();
    }

    function clampStatValue(input) {
        setStatValue(input, input.value);
    }

    function updateSummary() {
        const charName = charNameInput
            ? (charNameInput.value || 'Unnamed')
            : 'Unnamed';

        const summaryName = document.getElementById('summaryName');
        if (summaryName) {
            summaryName.textContent = charName;
        }

        const selectedClass = document.querySelector(
            'input[name="character_class_id"]:checked'
        );

        const className = selectedClass
            ? selectedClass.getAttribute('data-class')
            : '—';

        const statClass = document.getElementById('statClass');
        if (statClass) {
            statClass.textContent = className;
        }

        const selectedRace = document.querySelector(
            'input[name="character_race_id"]:checked'
        );

        const raceName = selectedRace
            ? selectedRace.getAttribute('data-race')
            : '—';

        const statRace = document.getElementById('statRace');
        if (statRace) {
            statRace.textContent = raceName;
        }

        const subtitle = selectedClass
            ? `${raceName} ${className}`
            : 'No class/race selected';

        const summarySubtitle =
            document.getElementById('summarySubtitle');

        if (summarySubtitle) {
            summarySubtitle.textContent = subtitle;
        }

        const selectedJob =
            jobSelect &&
            jobSelect.options[jobSelect.selectedIndex];

        const jobName = selectedJob
            ? selectedJob.getAttribute('data-job') || '—'
            : '—';

        const statJob = document.getElementById('statJob');

        if (statJob) {
            statJob.textContent = jobName;
        }

        const statLevel = document.getElementById('statLevel');

        if (statLevel) {
            statLevel.textContent =
                levelInput && levelInput.value
                    ? levelInput.value
                    : 1;
        }

        const statHP = document.getElementById('statHP');

        if (statHP) {
            statHP.textContent =
                hpInput && hpInput.value
                    ? hpInput.value
                    : 10;
        }

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
            const input = document.querySelector(
                `input[name="${statName}"]`
            );

            const target = document.getElementById(summaryId);

            if (target) {
                target.textContent =
                    input && input.value
                        ? input.value
                        : 0;
            }
        });

        classRadios.forEach(radio => {
            const card = radio.parentElement;

            if (card) {
                card.classList.toggle(
                    'selected',
                    radio.checked
                );
            }
        });

        raceRadios.forEach(radio => {
            const card = radio.parentElement;

            if (card) {
                card.classList.toggle(
                    'selected',
                    radio.checked
                );
            }
        });
    }

    statInputs.forEach(input => {
        input.dataset.previousValue =
            String(Number(input.value) || 0);

        input.addEventListener('wheel', function(event) {
            event.preventDefault();

            const currentValue =
                Number(this.value) || 0;

            const direction =
                event.deltaY < 0 ? 1 : -1;

            setStatValue(
                this,
                currentValue + direction
            );
        }, { passive: false });

        input.addEventListener('input', function() {
            clampStatValue(this);
        });

        input.addEventListener('change', function() {
            clampStatValue(this);
        });
    });

    document.querySelectorAll('.stat-button').forEach(button => {
        button.addEventListener('click', function() {
            const targetName =
                this.dataset.target || this.dataset.stat;

            const action = this.dataset.action;

            const input = document.querySelector(
                `input[name="${targetName}"]`
            );

            if (!input) return;

            const currentValue =
                Number(input.value) || 0;

            if (action === 'increase') {
                setStatValue(
                    input,
                    currentValue + 1
                );
            }

            if (action === 'decrease') {
                setStatValue(
                    input,
                    currentValue - 1
                );
            }
        });
    });

    if (hpInput) {
        hpInput.addEventListener('input', updateSummary);
        hpInput.addEventListener('change', updateSummary);
    }

    if (charNameInput) {
        charNameInput.addEventListener('input', updateSummary);
    }

    if (classRadios.length) {
        classRadios.forEach(radio => {
            radio.addEventListener('change', updateSummary);
        });
    }

    if (raceRadios.length) {
        raceRadios.forEach(radio => {
            radio.addEventListener('change', updateSummary);
        });
    }

    if (jobSelect) {
        jobSelect.addEventListener('change', updateSummary);
    }

    if (levelInput) {
        levelInput.addEventListener('input', updateSummary);
    }

    if (form) {
        form.addEventListener('submit', function(event) {
            if (getUsedPoints() > MaxPoints) {
                event.preventDefault();
            }
        });
    }

    document.addEventListener('click', function(event) {
        const button = event.target.closest('.d20-button');

        if (!button) return;

        const diceRoller = button.closest('.dice-roller');

        if (!diceRoller) return;

        const resultElement =
            diceRoller.querySelector('.d20-result');

        if (!resultElement) return;

        const result = Math.floor(Math.random() * 20) + 1;

        resultElement.textContent = result;

        resultElement.classList.remove('dice-rolling');

        void resultElement.offsetWidth;

        resultElement.classList.add('dice-rolling');
    });

    updatePointsDisplay();
    updateSummary();
});