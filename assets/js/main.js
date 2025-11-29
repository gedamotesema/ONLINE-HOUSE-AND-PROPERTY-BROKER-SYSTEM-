/**
 * GNK Housing Agency - Main JavaScript
 * Core functionality and interactivity
 * @version 1.0.0
 * @copyright 2025 GNK Housing Agency
 */

'use strict';

// ============================================
// SAMPLE PROPERTY DATA (20 Properties)
// ============================================
document.querySelector('.mobile-toggle').addEventListener('click', () => {
    document.querySelector('.nav-menu').classList.toggle('open');
});

const sampleProperties = [
    {
        id: 1,
        title: "Modern Downtown Apartment",
        type: "apartment",
        listingType: "rent",
        price: 2500,
        location: "New York, NY",
        bedrooms: 2,
        bathrooms: 2,
        sqft: 1200,
        image: "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&fit=crop&q=80",
        featured: true
    },
    {
        id: 2,
        title: "Luxury Beachfront Villa",
        type: "villa",
        listingType: "sale",
        price: 1250000,
        location: "Miami, FL",
        bedrooms: 5,
        bathrooms: 4,
        sqft: 4500,
        image: "https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&h=600&fit=crop&q=80",
        featured: true
    },
    {
        id: 3,
        title: "Cozy Suburban House",
        type: "house",
        listingType: "rent",
        price: 1800,
        location: "Austin, TX",
        bedrooms: 3,
        bathrooms: 2,
        sqft: 1800,
        image: "https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&h=600&fit=crop&q=80",
        featured: true
    },
    {
        id: 4,
        title: "Penthouse with City Views",
        type: "condo",
        listingType: "sale",
        price: 850000,
        location: "Chicago, IL",
        bedrooms: 3,
        bathrooms: 3,
        sqft: 2200,
        image: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&h=600&fit=crop&q=80",
        featured: true
    },
    {
        id: 5,
        title: "Spacious Family Home",
        type: "house",
        listingType: "sale",
        price: 425000,
        location: "Seattle, WA",
        bedrooms: 4,
        bathrooms: 3,
        sqft: 2800,
        image: "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop&q=80",
        featured: true
    },
    {
        id: 6,
        title: "Studio in Arts District",
        type: "apartment",
        listingType: "rent",
        price: 1500,
        location: "Los Angeles, CA",
        bedrooms: 1,
        bathrooms: 1,
        sqft: 650,
        image: "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop&q=80",
        featured: true
    },
    {
        id: 7,
        title: "Mountain View Cabin",
        type: "house",
        listingType: "sale",
        price: 380000,
        location: "Denver, CO",
        bedrooms: 3,
        bathrooms: 2,
        sqft: 1600,
        image: "https://images.unsplash.com/photo-1542718610-a1d656d1884c?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 8,
        title: "Urban Loft Space",
        type: "apartment",
        listingType: "rent",
        price: 2200,
        location: "Portland, OR",
        bedrooms: 2,
        bathrooms: 1,
        sqft: 1100,
        image: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 9,
        title: "Waterfront Estate",
        type: "villa",
        listingType: "sale",
        price: 2100000,
        location: "San Diego, CA",
        bedrooms: 6,
        bathrooms: 5,
        sqft: 5500,
        image: "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 10,
        title: "Historic Townhouse",
        type: "house",
        listingType: "rent",
        price: 3200,
        location: "Boston, MA",
        bedrooms: 4,
        bathrooms: 3,
        sqft: 2400,
        image: "https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 11,
        title: "Contemporary Condo",
        type: "condo",
        listingType: "sale",
        price: 520000,
        location: "Nashville, TN",
        bedrooms: 2,
        bathrooms: 2,
        sqft: 1400,
        image: "https://images.unsplash.com/photo-1567496898669-ee935f5f647a?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 12,
        title: "Garden Apartment",
        type: "apartment",
        listingType: "rent",
        price: 1900,
        location: "Philadelphia, PA",
        bedrooms: 2,
        bathrooms: 2,
        sqft: 1150,
        image: "https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 13,
        title: "Countryside Ranch",
        type: "house",
        listingType: "sale",
        price: 695000,
        location: "Nashville, TN",
        bedrooms: 5,
        bathrooms: 4,
        sqft: 3800,
        image: "https://images.unsplash.com/photo-1588880331179-bc9b93a8cb5e?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 14,
        title: "Downtown High-Rise",
        type: "apartment",
        listingType: "rent",
        price: 2800,
        location: "San Francisco, CA",
        bedrooms: 2,
        bathrooms: 2,
        sqft: 1300,
        image: "https://images.unsplash.com/photo-1515263487990-61b07816b324?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 15,
        title: "Mediterranean Villa",
        type: "villa",
        listingType: "sale",
        price: 1650000,
        location: "Phoenix, AZ",
        bedrooms: 5,
        bathrooms: 5,
        sqft: 4800,
        image: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 16,
        title: "Renovated Brownstone",
        type: "house",
        listingType: "sale",
        price: 925000,
        location: "Brooklyn, NY",
        bedrooms: 4,
        bathrooms: 3,
        sqft: 2600,
        image: "https://images.unsplash.com/photo-1572120360610-d971b9d7767c?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 17,
        title: "Lakefront Retreat",
        type: "house",
        listingType: "rent",
        price: 2600,
        location: "Minneapolis, MN",
        bedrooms: 3,
        bathrooms: 2,
        sqft: 2000,
        image: "https://images.unsplash.com/photo-1599809275671-b5942cabc7a2?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 18,
        title: "Modern Industrial Loft",
        type: "apartment",
        listingType: "rent",
        price: 2100,
        location: "Detroit, MI",
        bedrooms: 1,
        bathrooms: 1,
        sqft: 950,
        image: "https://images.unsplash.com/photo-1502672023488-70e25813eb80?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 19,
        title: "Luxury Skyline Condo",
        type: "condo",
        listingType: "sale",
        price: 740000,
        location: "Atlanta, GA",
        bedrooms: 3,
        bathrooms: 2,
        sqft: 1900,
        image: "https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?w=800&h=600&fit=crop&q=80",
        featured: false
    },
    {
        id: 20,
        title: "Charming Cottage",
        type: "house",
        listingType: "rent",
        price: 1600,
        location: "Charleston, SC",
        bedrooms: 2,
        bathrooms: 2,
        sqft: 1250,
        image: "https://images.unsplash.com/photo-1598228723793-52759bba239c?w=800&h=600&fit=crop&q=80",
        featured: false
    }
];

// ============================================
// DOM ELEMENTS
// ============================================

const elements = {
    header: document.getElementById('mainHeader'),
    mobileMenuBtn: document.getElementById('mobileMenuBtn'),
    navMenu: document.getElementById('navMenu'),
    backToTop: document.getElementById('backToTop'),
    heroSearchForm: document.getElementById('heroSearchForm'),
    featuredPropertiesGrid: document.getElementById('featuredPropertiesGrid'),
    testimonialsSlider: document.getElementById('testimonialsSlider')
};

// ============================================
// HEADER SCROLL BEHAVIOR
// ============================================

function initHeaderScroll() {
    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        // Add shadow when scrolled
        if (currentScroll > 50) {
            elements.header?.classList.add('scrolled');
        } else {
            elements.header?.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
}

// ============================================
// MOBILE MENU TOGGLE
// ============================================

function initMobileMenu() {
    elements.mobileMenuBtn?.addEventListener('click', () => {
        elements.mobileMenuBtn.classList.toggle('active');
        elements.navMenu?.classList.toggle('active');
        
        // Update ARIA attribute
        const isExpanded = elements.navMenu?.classList.contains('active');
        elements.mobileMenuBtn.setAttribute('aria-expanded', isExpanded);
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.navbar') && elements.navMenu?.classList.contains('active')) {
            elements.mobileMenuBtn?.classList.remove('active');
            elements.navMenu?.classList.remove('active');
        }
    });
}

// ============================================
// BACK TO TOP BUTTON
// ============================================

function initBackToTop() {
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            elements.backToTop?.classList.add('visible');
        } else {
            elements.backToTop?.classList.remove('visible');
        }
    });
    
    elements.backToTop?.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ============================================
// HERO SEARCH TABS
// ============================================

function initSearchTabs() {
    const searchTabs = document.querySelectorAll('.search-tab');
    
    searchTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            searchTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
        });
    });
}

// ============================================
// HERO SEARCH FORM SUBMISSION
// ============================================

function initSearchForm() {
    elements.heroSearchForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const location = document.getElementById('searchLocation')?.value;
        const propertyType = document.getElementById('searchPropertyType')?.value;
        const priceRange = document.getElementById('searchPriceRange')?.value;
        
        // Build query string
        const params = new URLSearchParams();
        if (location) params.append('location', location);
        if (propertyType) params.append('type', propertyType);
        if (priceRange) params.append('price', priceRange);
        
        // Redirect to properties page
        window.location.href = `properties.html?${params.toString()}`;
    });
}

// ============================================
// RENDER PROPERTY CARD
// ============================================

function createPropertyCard(property) {
    const priceFormatted = property.listingType === 'sale' 
        ? `$${(property.price / 1000).toFixed(0)}K`
        : `$${property.price.toLocaleString()}/mo`;
    
    const badgeText = property.listingType === 'rent' ? 'For Rent' : 'For Sale';
    const badgeClass = property.listingType === 'rent' ? '' : 'for-sale';
    
    return `
        <div class="property-card" data-id="${property.id}">
            <div class="property-card-image">
                <img src="${property.image}" alt="${property.title}" loading="lazy">
                <span class="property-badge ${badgeClass}">${badgeText}</span>
                <button class="property-favorite" aria-label="Add to favorites" data-id="${property.id}">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="property-card-content">
                <div class="property-price">
                    ${priceFormatted}
                    ${property.listingType === 'rent' ? '<span class="property-price-period">/month</span>' : ''}
                </div>
                <h3 class="property-title">${property.title}</h3>
                <div class="property-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${property.location}</span>
                </div>
                <div class="property-features">
                    <div class="property-feature">
                        <i class="fas fa-bed"></i>
                        <span>${property.bedrooms} Beds</span>
                    </div>
                    <div class="property-feature">
                        <i class="fas fa-bath"></i>
                        <span>${property.bathrooms} Baths</span>
                    </div>
                    <div class="property-feature">
                        <i class="fas fa-ruler-combined"></i>
                        <span>${property.sqft.toLocaleString()} sqft</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// ============================================
// RENDER FEATURED PROPERTIES
// ============================================

function renderFeaturedProperties() {
    if (!elements.featuredPropertiesGrid) return;
    
    const featuredProperties = sampleProperties.filter(p => p.featured).slice(0, 6);
    
    elements.featuredPropertiesGrid.innerHTML = featuredProperties
        .map(property => createPropertyCard(property))
        .join('');
    
    // Add click event to property cards
    elements.featuredPropertiesGrid.querySelectorAll('.property-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (!e.target.closest('.property-favorite')) {
                const propertyId = card.dataset.id;
                window.location.href = `property-detail.html?id=${propertyId}`;
            }
        });
    });
    
    // Add favorite button functionality
    initFavoriteButtons();
}

// ============================================
// FAVORITE BUTTONS
// ============================================

function initFavoriteButtons() {
    const favoriteButtons = document.querySelectorAll('.property-favorite');
    
    // Load favorites from localStorage
    const favorites = JSON.parse(localStorage.getItem('gnk_favorites') || '[]');
    
    favoriteButtons.forEach(button => {
        const propertyId = button.dataset.id;
        
        // Set initial state
        if (favorites.includes(propertyId)) {
            button.classList.add('active');
            button.querySelector('i').classList.replace('far', 'fas');
        }
        
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleFavorite(propertyId, button);
        });
    });
}

function toggleFavorite(propertyId, button) {
    let favorites = JSON.parse(localStorage.getItem('gnk_favorites') || '[]');
    const icon = button.querySelector('i');
    
    if (favorites.includes(propertyId)) {
        // Remove from favorites
        favorites = favorites.filter(id => id !== propertyId);
        button.classList.remove('active');
        icon.classList.replace('fas', 'far');
    } else {
        // Add to favorites
        favorites.push(propertyId);
        button.classList.add('active');
        icon.classList.replace('far', 'fas');
    }
    
    localStorage.setItem('gnk_favorites', JSON.stringify(favorites));
}

// ============================================
// ANIMATED COUNTERS (Hero Stats)
// ============================================

function initAnimatedCounters() {
    const counters = document.querySelectorAll('.stat-number');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.count);
                animateCounter(counter, target);
                observer.unobserve(counter);
            }
        });
    }, observerOptions);
    
    counters.forEach(counter => observer.observe(counter));
}

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const duration = 2000;
    const stepTime = duration / 50;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, stepTime);
}

// ============================================
// TESTIMONIALS SLIDER
// ============================================

function initTestimonialsSlider() {
    const testimonials = document.querySelectorAll('.testimonial-card');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.slider-arrow-prev');
    const nextBtn = document.querySelector('.slider-arrow-next');
    
    if (!testimonials.length) return;
    
    let currentIndex = 0;
    
    function showTestimonial(index) {
        testimonials.forEach(t => t.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        
        testimonials[index].classList.add('active');
        dots[index]?.classList.add('active');
    }
    
    function nextTestimonial() {
        currentIndex = (currentIndex + 1) % testimonials.length;
        showTestimonial(currentIndex);
    }
    
    function prevTestimonial() {
        currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
        showTestimonial(currentIndex);
    }
    
    // Next button
    nextBtn?.addEventListener('click', nextTestimonial);
    
    // Previous button
    prevBtn?.addEventListener('click', prevTestimonial);
    
    // Dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentIndex = index;
            showTestimonial(currentIndex);
        });
    });
    
    // Auto-play
    setInterval(nextTestimonial, 5000);
}

// ============================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ============================================

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ============================================
// SCROLL INDICATOR CLICK
// ============================================

function initScrollIndicator() {
    const scrollIndicator = document.querySelector('.scroll-indicator');
    
    scrollIndicator?.addEventListener('click', () => {
        const featuredSection = document.getElementById('featuredSection');
        featuredSection?.scrollIntoView({ behavior: 'smooth' });
    });
}

// ============================================
// LAZY LOADING IMAGES
// ============================================

function initLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

// ============================================
// INITIALIZE ALL FUNCTIONS
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    initHeaderScroll();
    initMobileMenu();
    initBackToTop();
    initSearchTabs();
    initSearchForm();
    renderFeaturedProperties();
    initAnimatedCounters();
    initTestimonialsSlider();
    initSmoothScroll();
    initScrollIndicator();
    initLazyLoading();
    
    console.log('🏠 GNK Housing Agency initialized successfully!');
});

// ============================================
// EXPORT FOR USE IN OTHER PAGES
// ============================================

window.GNKHousing = {
    properties: sampleProperties,
    createPropertyCard,
    toggleFavorite
};
