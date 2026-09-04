// Character Creator - Real-time summary panel update
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const charNameInput = document.querySelector('input[name="character_name"]');
    const classRadios = document.querySelectorAll('input[name="character_class_id"]');
    const raceRadios = document.querySelectorAll('input[name="character_race_id"]');
    const jobSelect = document.querySelector('select[name="character_job_id"]');
    const levelInput = document.querySelector('input[name="level"]');
    const hpInput = document.querySelector('input[name="hp_max"]');

    function updateSummary() {
        // Update name
        const charName = charNameInput.value || 'Unnamed';
        document.getElementById('summaryName').textContent = charName;

        // Update selected class
        const selectedClass = document.querySelector('input[name="character_class_id"]:checked');
        const className = selectedClass ? selectedClass.getAttribute('data-class') : '—';
        document.getElementById('statClass').textContent = className;

        // Update selected race
        const selectedRace = document.querySelector('input[name="character_race_id"]:checked');
        const raceName = selectedRace ? selectedRace.getAttribute('data-race') : '—';
        document.getElementById('statRace').textContent = raceName;

        // Update subtitle
        const subtitle = selectedClass ? `${className} ${raceName}` : 'No class/race selected';
        document.getElementById('summarySubtitle').textContent = subtitle;

        // Update job
        const selectedJob = jobSelect.options[jobSelect.selectedIndex];
        const jobName = selectedJob.getAttribute('data-job') || '—';
        document.getElementById('statJob').textContent = jobName;

        // Update level and HP
        document.getElementById('statLevel').textContent = levelInput.value || 1;
        document.getElementById('statHP').textContent = hpInput.value || 10;

        // Visual feedback for selected options
        classRadios.forEach(radio => {
            radio.parentElement.classList.toggle('selected', radio.checked);
        });

        raceRadios.forEach(radio => {
            radio.parentElement.classList.toggle('selected', radio.checked);
        });
    }

    charNameInput.addEventListener('input', updateSummary);
    classRadios.forEach(radio => radio.addEventListener('change', updateSummary));
    raceRadios.forEach(radio => radio.addEventListener('change', updateSummary));
    jobSelect.addEventListener('change', updateSummary);
    levelInput.addEventListener('input', updateSummary);
    hpInput.addEventListener('input', updateSummary);

    // Initial update
    updateSummary();
});
