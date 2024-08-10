document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.stars').forEach(function (starsContainer) {
        const starInputs = Array.from(starsContainer.querySelectorAll('.star'));
        const ratingOptionId = starsContainer.dataset.ratingOptionId;
        const currentRatingValue = parseInt(starsContainer.dataset.current);

        function updateRatingValue(selectedValue) {
            const ratingValueInput = starsContainer.parentNode.querySelector(`input[type="hidden"][name="ratings[${ratingOptionId}]"]`);
            ratingValueInput.value = selectedValue;
        }

        starInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                const selectedValue = parseInt(this.value);
                updateRatingValue(selectedValue);
            });
        });

        // Select the radio button with the current rating value
        const currentRatingInput = starInputs.find(input => parseInt(input.value) === currentRatingValue);
        if (currentRatingInput) {
            currentRatingInput.checked = true;
        }

        // Initialize the hidden input with the current value
        updateRatingValue(currentRatingValue);
    });
});