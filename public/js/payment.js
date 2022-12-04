const stripe = Stripe('pk_test_51M4Vi9GuP1FelhhbRY9gL4oeLc97jSO5YhFDN3pGG8j22f96UkImgTu0XbEmKN6S9MvR1ivOQHIAVxkMIiqNxRnV00IArqAfeB');

const elements = stripe.elements({});

const card = elements.create("card");

card.mount('#payment-element');
card.on("change", function (event) {
    document.querySelector("button").disabled = event.empty;
    document.querySelector("#payment-message").textContent = event.error ? event.error.message : 
    "";
    });

const form = document.getElementById("payment-form");

form.addEventListener("submit", function(event) {
    event.preventDefault();
    // Complete payment when the submit button is clicked
    stripe
        .handleCardPayment(
            clientSecret,
            card,
            {
            payment_method_data: {
                card: card
            }
        })
            .then(function(result) {
                if (result.error) {
                    //show error to your customer
                    console.log(result.error.message);
                } else {
                    // the payment succeeded!
                    window.location.href = redirectAfterSuccessUrl;
                }
    });
}); 