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


const shopNowBtn = document.querySelector('.hero .btn-primary');

if (shopNowBtn) {
    shopNowBtn.addEventListener('click', function(e) {

    });
}


//ADD TO CART FUNCTIONALITy
const addToCartBtns = document.querySelectorAll('.btn-add');

addToCartBtns.forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const productId = this.dataset.id;
        const productName = this.dataset.name;
        const productPrice = this.dataset.price;
        const productImage = this.dataset.image;
        const productStock = this.dataset.stock;
        
        console.log('Adding to cart:', {
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            stock: productStock
        });
        
        const url = 'cart.php?add=1&id=' + encodeURIComponent(productId) + 
                    '&name=' + encodeURIComponent(productName) + 
                    '&price=' + encodeURIComponent(productPrice) + 
                    '&image=' + encodeURIComponent(productImage) + 
                    '&stock=' + encodeURIComponent(productStock) + 
                    '&qty=1';
        
        window.location.href = url;
    });
});


const buyMoreBtn = document.querySelector('.buy-more-content .btn-primary');

if (buyMoreBtn) {
    buyMoreBtn.addEventListener('click', function(e) {
     
    });
}

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


document.querySelectorAll('.footer-col ul li a').forEach(function(link) {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});


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

// DROPDOWN MENU
const dropdownToggle = document.getElementById('menuToggle');
const dropdownMenu = document.getElementById('dropdownMenu');

if (dropdownToggle && dropdownMenu) {
    dropdownToggle.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownMenu.classList.toggle('active');
        
        const icon = this.querySelector('i');
        if (icon) {
            if (dropdownMenu.classList.contains('active')) {
                icon.className = 'fas fa-times';
            } else {
                icon.className = 'fas fa-bars';
            }
        }
    });
    
    document.addEventListener('click', function(event) {
        if (dropdownMenu && dropdownToggle) {
            if (!dropdownMenu.contains(event.target) && !dropdownToggle.contains(event.target)) {
                dropdownMenu.classList.remove('active');
                const icon = dropdownToggle.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-bars';
                }
            }
        }
    });
    
    if (dropdownMenu) {
        dropdownMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                dropdownMenu.classList.remove('active');
                const icon = dropdownToggle.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-bars';
                }
            });
        });
    }
}

console.log('✅ 2THSND4 website is fully functional!');


function showLogoutModal(event) {
    event.preventDefault();
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.style.display = 'none';
    }
}


document.addEventListener('click', function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});


// POPUP FUNCTIONS
function showSuccessPopup() {
    const popup = document.getElementById('successPopup');
    if (popup) {
        popup.style.display = 'flex';
    }
}

function showErrorPopup(message) {
    const popup = document.getElementById('errorPopup');
    const errorText = document.getElementById('errorMessageText');
    if (popup && errorText) {
        errorText.textContent = message || 'Something went wrong. Please try again.';
        popup.style.display = 'flex';
    } else {
        // Fallback if error popup doesn't exist
        alert(message || 'Something went wrong. Please try again.');
    }
}

function closePopup(popupId) {
    const popup = document.getElementById(popupId);
    if (popup) {
        popup.style.display = 'none';
    }
}

function closeSuccessPopup() {
    closePopup('successPopup');
}

function closeErrorPopup() {
    closePopup('errorPopup');
}

// Close popups on background click
document.addEventListener('click', function(event) {
    const successPopup = document.getElementById('successPopup');
    const errorPopup = document.getElementById('errorPopup');
    
    if (successPopup && event.target === successPopup) {
        successPopup.style.display = 'none';
    }
    if (errorPopup && event.target === errorPopup) {
        errorPopup.style.display = 'none';
    }
});

// Close popups with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePopup('successPopup');
        closePopup('errorPopup');
    }
});

//CONTACT FORM 
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
      
        contactForm.onsubmit = null;
        contactForm.addEventListener('submit', function(e) {
            return true;
        });
    }
});

const originalClosePopup = window.closePopup;
window.closePopup = function(popupId) {
    if (popupId) {
        const popup = document.getElementById(popupId);
        if (popup) {
            popup.style.display = 'none';
        }
    } else {

        const successPopup = document.getElementById('successPopup');
        const errorPopup = document.getElementById('errorPopup');
        if (successPopup) successPopup.style.display = 'none';
        if (errorPopup) errorPopup.style.display = 'none';
    }
};

const originalShowSuccessPopup = window.showSuccessPopup;
window.showSuccessPopup = function() {
    const popup = document.getElementById('successPopup');
    if (popup) {
        popup.style.display = 'flex';
    }
};

const originalShowErrorPopup = window.showErrorPopup;
window.showErrorPopup = function(message) {
    const popup = document.getElementById('errorPopup');
    const errorText = document.getElementById('errorMessageText');
    if (popup && errorText) {
        errorText.textContent = message || 'Something went wrong. Please try again.';
        popup.style.display = 'flex';
    } else {
        alert(message || 'Something went wrong. Please try again.');
    }
};