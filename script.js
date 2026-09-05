// ============================================
// 2THSND4 - COMPLETE FUNCTIONALITY
// ============================================

// ===== 1. MOBILE MENU TOGGLE =====
const mobileToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');

if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', function() {
        navLinks.classList.toggle('active');
        this.innerHTML = navLinks.classList.contains('active') 
            ? '<i class="fas fa-times"></i>' 
            : '<i class="fas fa-bars"></i>';
    });

    document.querySelectorAll('.nav-links a').forEach(function(link) {
        link.addEventListener('click', function() {
            navLinks.classList.remove('active');
            mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
        });
    });
}

// ===== 2. NAVBAR - ACTIVE LINK =====
const navLinkItems = document.querySelectorAll('.nav-links a');

function removeActiveClass() {
    navLinkItems.forEach(function(link) {
        link.classList.remove('active');
    });
}

navLinkItems.forEach(function(link) {
    link.addEventListener('click', function(e) {
        removeActiveClass();
        this.classList.add('active');
        
        const href = this.getAttribute('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
});

// ===== 3. AUTO-HIGHLIGHT ON SCROLL =====
const sections = document.querySelectorAll('section[id]');

if (sections.length > 0) {
    window.addEventListener('scroll', function() {
        let current = '';
        const scrollY = window.scrollY + 200;
        
        sections.forEach(function(section) {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            
            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinkItems.forEach(function(link) {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
}

// ===== 4. SEARCH DROPDOWN TOGGLE =====
const searchToggle = document.getElementById('searchToggle');
const searchDropdown = document.getElementById('searchDropdown');
const searchInput = document.getElementById('searchInput');

if (searchToggle && searchDropdown) {
    searchToggle.addEventListener('click', function(e) {
        e.preventDefault();
        searchDropdown.classList.toggle('active');
        if (searchDropdown.classList.contains('active')) {
            searchInput.focus();
        }
    });

    document.addEventListener('click', function(e) {
        if (!searchToggle.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.remove('active');
        }
    });

    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    window.location.href = 'shop.php?search=' + encodeURIComponent(searchTerm);
                    searchDropdown.classList.remove('active');
                    this.value = '';
                }
            }
        });
    }

    const searchBtn = searchDropdown.querySelector('button');
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                window.location.href = 'shop.php?search=' + encodeURIComponent(searchTerm);
                searchDropdown.classList.remove('active');
                searchInput.value = '';
            }
        });
    }
}

// ===== 5. SHOP NOW BUTTON =====
const shopNowBtn = document.querySelector('.hero .btn-primary');

if (shopNowBtn) {
    shopNowBtn.addEventListener('click', function(e) {
        // Mo-redirect sa shop.php
    });
}

// ===== 6. ADD TO CART FUNCTIONALITY =====
const addToCartBtns = document.querySelectorAll('.btn-add');

addToCartBtns.forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Gamit ang closest para ma-kuha ang parent product-card
        const card = this.closest('.product-card');
        
        if (!card) {
            console.error('Product card not found!');
            return;
        }
        
        // Kuhaon ang product details
        const productName = card.querySelector('h3') ? card.querySelector('h3').textContent : 'Product';
        const productPrice = card.querySelector('.price') ? card.querySelector('.price').textContent : '0.00';
        const cleanPrice = productPrice.replace(/[₱,]/g, '').trim();
        
        const imgElement = card.querySelector('.product-image img');
        let imagePath = 'default.jpg';
        if (imgElement) {
            const src = imgElement.getAttribute('src');
            imagePath = src.split('/').pop();
        }
        
        // ===== GET STOCK FROM DATA ATTRIBUTE =====
        const stock = this.dataset.stock || 10;
        
        // I-print sa console para ma-verify
        console.log('Adding to cart:', {
            name: productName,
            price: cleanPrice,
            image: imagePath,
            stock: stock
        });
        
        // I-redirect sa cart.php with stock
        const url = 'cart.php?add=1&name=' + encodeURIComponent(productName) + 
                    '&price=' + encodeURIComponent(cleanPrice) + 
                    '&image=' + encodeURIComponent(imagePath) + 
                    '&qty=1' +
                    '&stock=' + encodeURIComponent(stock);
        
        window.location.href = url;
    });
});

// ===== 7. BUY MORE BUTTON =====
const buyMoreBtn = document.querySelector('.buy-more-content .btn-primary');

if (buyMoreBtn) {
    buyMoreBtn.addEventListener('click', function(e) {
        // Wala nay e.preventDefault();
        // Mo-redirect na sa shop.php kay naay href ang button
    });
}

// ===== 8. GET YOURS NOW BUTTON =====
const getYoursBtn = document.querySelector('.ratings-section .btn-primary');

if (getYoursBtn) {
    getYoursBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const featuredSection = document.querySelector('.featured-collection');
        if (featuredSection) {
            featuredSection.scrollIntoView({ behavior: 'smooth' });
        }
    });
}

// ===== 9. FOOTER LINKS =====
document.querySelectorAll('.footer-col ul li a').forEach(function(link) {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});

// ===== 10. CAROUSEL - AUTO SCROLL =====
const carousel = document.querySelector('.products-carousel');

if (carousel) {
    let isDown = false;
    let startX;
    let scrollLeft;

    carousel.addEventListener('mousedown', function(e) {
        isDown = true;
        startX = e.pageX - this.offsetLeft;
        scrollLeft = this.scrollLeft;
        this.style.cursor = 'grabbing';
    });

    carousel.addEventListener('mouseleave', function() {
        isDown = false;
        this.style.cursor = 'grab';
    });

    carousel.addEventListener('mouseup', function() {
        isDown = false;
        this.style.cursor = 'grab';
    });

    carousel.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - this.offsetLeft;
        const walk = (x - startX) * 2;
        this.scrollLeft = scrollLeft - walk;
    });

    let autoScroll = setInterval(function() {
        if (!carousel) return;
        const scrollAmount = carousel.scrollLeft + 300;
        if (scrollAmount >= carousel.scrollWidth - carousel.clientWidth) {
            carousel.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            carousel.scrollTo({ left: scrollAmount, behavior: 'smooth' });
        }
    }, 5000);

    carousel.addEventListener('mouseenter', function() {
        clearInterval(autoScroll);
    });

    carousel.addEventListener('mouseleave', function() {
        autoScroll = setInterval(function() {
            if (!carousel) return;
            const scrollAmount = carousel.scrollLeft + 300;
            if (scrollAmount >= carousel.scrollWidth - carousel.clientWidth) {
                carousel.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                carousel.scrollTo({ left: scrollAmount, behavior: 'smooth' });
            }
        }, 5000);
    });
}

// ============================================
// DROPDOWN MENU TOGGLE (Para sa ☰)
// ============================================
const dropdownToggle = document.getElementById('menuToggle');
const dropdownMenu = document.getElementById('dropdownMenu');

if (dropdownToggle && dropdownMenu) {
    dropdownToggle.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownMenu.classList.toggle('active');
        
        const icon = this.querySelector('i');
        if (dropdownMenu.classList.contains('active')) {
            icon.className = 'fas fa-times';
        } else {
            icon.className = 'fas fa-bars';
        }
    });
    
    document.addEventListener('click', function(event) {
        if (!dropdownMenu.contains(event.target) && !dropdownToggle.contains(event.target)) {
            dropdownMenu.classList.remove('active');
            const icon = dropdownToggle.querySelector('i');
            icon.className = 'fas fa-bars';
        }
    });
    
    dropdownMenu.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
            dropdownMenu.classList.remove('active');
            const icon = dropdownToggle.querySelector('i');
            icon.className = 'fas fa-bars';
        });
    });
}

console.log('✅ 2THSND4 website is fully functional!');