document.addEventListener("DOMContentLoaded", () => {
    document.body.addEventListener("click", (event) => {
      const addToCartButton = event.target.closest(".add-to-cart-button")
      if (addToCartButton) {
        const productId = addToCartButton.dataset.productId
        const quantity = Number.parseInt(addToCartButton.dataset.quantity, 10)
        addToCart(productId, quantity, addToCartButton)
      }
    })
  })

  function addToCart(productId, quantity, button) {
    button.disabled = true
    const originalText = button.querySelector(".add-to-cart-text").textContent
    button.querySelector(".add-to-cart-text").textContent = "Adicionando..."

    fetch("/carrinho/items", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        Accept: "application/json",
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: quantity,
      }),
    })
      .then((response) => {
        if (!response.ok) {
          if (response.status === 401) {
            throw new Error("Unauthorized")
          }
          return response.json().then((err) => {
            throw err
          })
        }
        return response.json()
      })
      .then((data) => {
        console.log("Dados recebidos:", data)
        if (data.success) {
          window.showToast(data.message, "success")
          if (data.cartCount !== undefined) {
            updateCartCount(data.cartCount)
          }
        } else {
          window.showToast(data.message || "Erro ao adicionar ao carrinho", "error")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        if (error.message === "Unauthorized") {
          window.showToast("Por favor, faça login para adicionar itens ao carrinho.", "error")
        } else {
          window.showToast(
            error.message ||
              "Ocorreu um erro ao adicionar o item ao seu carrinho. Por favor, tente novamente mais tarde.",
            "error",
          )
        }
      })
      .finally(() => {
        button.disabled = false
        button.querySelector(".add-to-cart-text").textContent = originalText
      })
  }

  function updateCartCount(count) {
    const cartCountElement = document.getElementById("cart-count")
    if (cartCountElement) {
      cartCountElement.textContent = count
    }
  }

