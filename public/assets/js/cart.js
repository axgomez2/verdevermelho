document.addEventListener("DOMContentLoaded", () => {
    // Recuperar contagem do carrinho local se não tiver do servidor
    if (!document.querySelector('.cart-count')) {
      const localCartCount = localStorage.getItem('cart-count') || '0';
      if (parseInt(localCartCount) > 0) {
        updateCartCount(parseInt(localCartCount));
      }
    }

    // Listener para botões de adicionar ao carrinho
    document.body.addEventListener("click", (event) => {
      const addToCartButton = event.target.closest(".add-to-cart-button")
      if (addToCartButton) {
        // Prevenir cliques múltiplos
        if (addToCartButton.disabled) return;
        
        const productId = addToCartButton.dataset.productId
        const maxQuantity = parseInt(addToCartButton.dataset.maxQuantity || '0', 10)
        const quantity = Number.parseInt(addToCartButton.dataset.quantity, 10)
        
        // Verificar se a quantidade solicitada é maior que o estoque
        if (maxQuantity > 0 && quantity > maxQuantity) {
          window.showToast(`Quantidade indisponível. Apenas ${maxQuantity} unidade(s) em estoque.`, "error");
          return;
        }
        
        addToCart(productId, quantity, addToCartButton)
      }

      // Listener para botões de favoritos
      const wishlistButton = event.target.closest(".wishlist-button")
      if (wishlistButton) {
        const productId = wishlistButton.dataset.productId
        const productType = wishlistButton.dataset.productType
        // toggleWishlist(wishlistButton, productType, productId)
      }
      
      // Listener para botões de quantidade no carrinho
      const qtyButton = event.target.closest('.qty-btn');
      if (qtyButton) {
        const input = qtyButton.closest('.qty-control').querySelector('input');
        const maxQty = parseInt(input.dataset.maxQuantity || '99', 10);
        const currentQty = parseInt(input.value, 10);
        
        if (qtyButton.classList.contains('qty-inc')) {
          // Incrementar quantidade
          if (currentQty < maxQty) {
            input.value = currentQty + 1;
            input.dispatchEvent(new Event('change'));
          } else {
            window.showToast(`Quantidade máxima disponível: ${maxQty}`, "error");
          }
        } else if (qtyButton.classList.contains('qty-dec')) {
          // Decrementar quantidade (mínimo 1)
          if (currentQty > 1) {
            input.value = currentQty - 1;
            input.dispatchEvent(new Event('change'));
          }
        }
      }
    })
  })

  // Função para gerenciar o carrinho local para usuários não logados
  function manageLocalCart(productId, quantity, productInfo = null) {
    // Obter o carrinho atual do localStorage
    let localCart = JSON.parse(localStorage.getItem('local-cart') || '[]')
    
    // Verificar se o produto já está no carrinho
    const existingItemIndex = localCart.findIndex(item => item.product_id === productId)
    
    if (existingItemIndex >= 0) {
      // Atualizar a quantidade
      localCart[existingItemIndex].quantity += quantity
      
      // Se fornecido productInfo com max_quantity, verificar limites
      if (productInfo && productInfo.max_quantity) {
        const maxQty = parseInt(productInfo.max_quantity, 10)
        if (localCart[existingItemIndex].quantity > maxQty) {
          localCart[existingItemIndex].quantity = maxQty
          window.showToast(`Quantidade máxima disponível: ${maxQty}`, "warning")
        }
      }
    } else {
      // Adicionar novo item
      localCart.push({
        product_id: productId,
        quantity: quantity,
        info: productInfo || {}
      })
    }
    
    // Salvar carrinho atualizado
    localStorage.setItem('local-cart', JSON.stringify(localCart))
    
    // Atualizar contagem total
    const totalItems = localCart.reduce((total, item) => total + item.quantity, 0)
    localStorage.setItem('cart-count', totalItems)
    
    return totalItems
  }
  
  function addToCart(productId, quantity, button) {
    button.disabled = true
    const originalText = button.querySelector(".add-to-cart-text")?.textContent || "Adicionar"
    if (button.querySelector(".add-to-cart-text")) {
      button.querySelector(".add-to-cart-text").textContent = "Adicionando..."
    }
    
    // Dados adicionais do produto
    const productInfo = {
      name: button.dataset.productName || '',
      price: button.dataset.productPrice || '',
      image: button.dataset.productImage || '',
      max_quantity: button.dataset.maxQuantity || ''
    }

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
          // Mostrar toast com botão de login, mas ainda adicionar ao cart local
          window.showToast(
            "Você pode adicionar itens ao carrinho sem login, mas precisará fazer login para finalizar a compra.",
            "info",
            {
              actionButton: {
                text: "Fazer Login",
                onClick: function() {
                  window.dispatchEvent(new CustomEvent('open-login-modal'))
                }
              },
              duration: 8000
            }
          )
          
          // Adicionar ao carrinho local e atualizar contador
          const totalItems = manageLocalCart(productId, quantity, productInfo)
          updateCartCount(totalItems)
        } else {
          window.showToast(
            error.message ||
              "Ocorreu um erro ao adicionar o item ao seu carrinho. Por favor, tente novamente mais tarde.",
            "error"
          )
        }
      })
      .finally(() => {
        button.disabled = false
        button.querySelector(".add-to-cart-text").textContent = originalText
      })
  }

  function updateCartCount(count) {
    // Atualiza o badge do carrinho
    const cartBadge = document.querySelector("[data-cart-count]")
    if (cartBadge) {
      if (count > 0) {
        cartBadge.textContent = count
        cartBadge.classList.remove("hidden")
      } else {
        cartBadge.classList.add("hidden")
      }
    }
  }

  function updateWishlistCount(count) {
    // Atualiza o badge dos favoritos
    const wishlistBadge = document.querySelector("[data-wishlist-count]")
    if (wishlistBadge) {
      if (count > 0) {
        wishlistBadge.textContent = count
        wishlistBadge.classList.remove("hidden")
      } else {
        wishlistBadge.classList.add("hidden")
      }
    }
  }

  // function toggleWishlist(button, type, id) {
  //   fetch(`/wishlist/toggle/${type}/${id}`, {
  //     method: "POST",
  //     headers: {
  //       "Content-Type": "application/json",
  //       "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
  //       Accept: "application/json",
  //     },
  //   })
  //     .then((response) => {
  //       if (!response.ok) {
  //         if (response.status === 401) {
  //           throw new Error("Unauthorized")
  //         }
  //         return response.json().then((err) => {
  //           throw err
  //         })
  //       }
  //       return response.json()
  //     })
  //     .then((data) => {
  //       if (data.success) {
  //         // Atualiza o ícone
  //         const icon = button.querySelector("i")
  //         icon.classList.toggle("fa-regular")
  //         icon.classList.toggle("fa-solid")
  //         icon.classList.toggle("text-red-500")

  //         // Atualiza a contagem
  //         if (data.wishlistCount !== undefined) {
  //           updateWishlistCount(data.wishlistCount)
  //         }

  //         window.showToast(data.message, "success")
  //       } else {
  //         window.showToast(data.message || "Erro ao atualizar favoritos", "error")
  //       }
  //     })
  //     .catch((error) => {
  //       console.error("Error:", error)
  //       if (error.message === "Unauthorized") {
  //         window.showToast("Por favor, faça login para gerenciar seus favoritos.", "error")
  //       } else {
  //         window.showToast(
  //           error.message || "Ocorreu um erro ao atualizar seus favoritos. Por favor, tente novamente mais tarde.",
  //           "error",
  //         )
  //       }
  //     })
  // }
