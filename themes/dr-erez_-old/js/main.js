document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('masthead');
    const body = document.body;
    
    if (!header) {
        return;
    }
    
    const offset = header.offsetTop;
    let headerHeight = 0;

    function updateStickyHeader() {
        if (headerHeight === 0) {
            headerHeight = header.offsetHeight;
            document.documentElement.style.setProperty('--sticky-header-height', headerHeight + 'px');
        }
        
        if (window.scrollY > offset) {
            header.classList.add('is-sticky');
            body.classList.add('has-sticky-header');
        } else {
            header.classList.remove('is-sticky');
            body.classList.remove('has-sticky-header');
        }
    }

    window.addEventListener('scroll', updateStickyHeader);
    window.addEventListener('resize', updateStickyHeader);
    updateStickyHeader();
});