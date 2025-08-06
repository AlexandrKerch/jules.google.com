document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.tariff-selector-container');
    if (!container) {
        return;
    }

    container.addEventListener('click', function (event) {
        const button = event.target.closest('.choose-tariff-btn');
        if (!button) {
            return;
        }
        event.preventDefault();

        const card = button.closest('.tariff-card');
        if (!card || card.classList.contains('selected')) {
            // Don't do anything if the selected card is clicked again
            return;
        }

        const tariffName = card.dataset.tariffName;
        if (!tariffName) {
            console.error('Tariff name not found in data-tariff-name attribute.');
            return;
        }

        // Add a visual indicator that something is happening
        container.style.opacity = '0.7';
        const buttons = container.querySelectorAll('.choose-tariff-btn');
        buttons.forEach(btn => btn.disabled = true);

        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=select_tariff&tariff_name=' + encodeURIComponent(tariffName) + '&sessid=' + (window.BX ? window.BX.bitrix_sessid() : '')
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update the UI
                container.querySelectorAll('.tariff-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
            } else {
                // In a real app, you might have a more sophisticated notification system
                alert('Error: ' + (data.message || 'Could not update your tariff selection.'));
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            alert('An error occurred while selecting the tariff. Please try again.');
        })
        .finally(() => {
            // Restore the UI
            container.style.opacity = '1';
            buttons.forEach(btn => btn.disabled = false);
        });
    });
});
