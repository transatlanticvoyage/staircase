/**
 * Weasel Contact Form Footer JavaScript
 * Theme-based contact form functionality that can be updated via theme updates
 */

(function(){
    const form = document.getElementById('estimateForm');
    if(!form) return;
    
    // Capture page URL
    const urlField = form.querySelector('input[name="page_url"]');
    if (urlField) urlField.value = window.location.href;
    
    const card = form.querySelector('.est-card');
    const steps = Array.prototype.slice.call(form.querySelectorAll('.form-step'));
    const live = document.getElementById('est-aria-live');
    const thankyou = document.getElementById('thankyouPanel');
    
    const stepHeadings = {
        1: document.getElementById('step1-heading'),
        2: document.getElementById('step2-heading')
    };
    
    function announce(msg){ 
        if(live) live.textContent = msg; 
    }
    
    function visibleIndex(){
        for (let i=0; i<steps.length; i++){ 
            if(!steps[i].classList.contains('est-hidden')) return i; 
        }
        return 0;
    }
    
    function showStep(i){
        steps.forEach((s,k)=>{
            const hide = (k!==i);
            s.classList.toggle('est-hidden', hide);
            s.setAttribute('aria-hidden', hide ? 'true':'false');
        });
        const stepNum = i+1;
        const h = stepHeadings[stepNum];
        if(h){ 
            h.setAttribute('tabindex','-1'); 
            h.focus({preventScroll:false}); 
        }
        announce('Step ' + stepNum + ' of ' + steps.length);
        // clear any summary when moving
        const sum = document.getElementById('errorSummary');
        if (sum) { 
            sum.hidden = true; 
            sum.textContent=''; 
        }
    }
    
    function clearErrors(scope){
        const fields = scope.querySelectorAll('input,select,textarea');
        fields.forEach(el=>{
            el.classList.remove('est-err');
            el.setAttribute('aria-invalid','false');
            const wrap = el.closest('.est-block') || el.closest('.est-fs') || el.parentElement;
            if (!wrap) return;
            const old = wrap.querySelector('.est-error-msg');
            if (old) old.remove();
        });
    }
    
    // inline message helper
    function addError(el, msg){
        const wrap = el.closest('.est-block') || el.closest('.est-fs') || el.parentElement;
        if (!wrap) return;
        el.classList.add('est-err');
        el.setAttribute('aria-invalid','true');
        const m = document.createElement('div');
        m.className = 'est-error-msg';
        m.textContent = msg;
        m.id = 'error-' + (el.id || Math.random().toString(36).substr(2, 9));
        wrap.appendChild(m);
        // Associate error message with field
        el.setAttribute('aria-describedby', (el.getAttribute('aria-describedby') || '') + ' ' + m.id);
    }
    
    // Announce error count when validation fails
    function announceErrors(errorCount){
        if (errorCount > 0){
            const plural = errorCount === 1 ? 'error' : 'errors';
            announce(`${errorCount} ${plural} found. Please fix the highlighted fields.`);
        }
    }
    
    function validateStep(i){
        const step = steps[i];
        clearErrors(step);
        let errorCount = 0;
        let firstErrorField = null;
        
        const required = step.querySelectorAll('[required]');
        // For radios: check by group
        const radioNames = new Set();
        required.forEach(el=>{
            if (el.type === 'radio') radioNames.add(el.name);
        });
        radioNames.forEach(name=>{
            const checked = step.querySelector('input[type="radio"][name="'+name+'"]:checked');
            if (!checked){
                errorCount++;
                const anyRadio = step.querySelector('input[type="radio"][name="'+name+'"]');
                if (!firstErrorField) firstErrorField = anyRadio;
                addError(anyRadio, 'Please choose an option.');
            }
        });
        
        // Non-radio requireds
        required.forEach(el=>{
            if (el.type === 'radio') return;
            const v = (el.value || '').trim();
            if (!v){
                errorCount++;
                if (!firstErrorField) firstErrorField = el;
                addError(el, 'This field is required.');
            }
            if (el.id === 'zip' && v && !/^\d{5}$/.test(v)){
                errorCount++;
                if (!firstErrorField) firstErrorField = el;
                addError(el, 'Enter a valid 5-digit ZIP.');
            }
            if (el.id === 'email' && v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)){
                errorCount++;
                if (!firstErrorField) firstErrorField = el;
                addError(el, 'Enter a valid email address.');
            }
        });
        
        // Announce errors and focus first error field
        if (errorCount > 0){
            announceErrors(errorCount);
            if (firstErrorField){
                // Give screen reader time to announce before focusing
                setTimeout(()=>{ firstErrorField.focus(); }, 100);
            }
        }
        
        // error summary
        const sum = document.getElementById('errorSummary');
        if (errorCount > 0 && sum && i===1){
            sum.hidden = false;
            const plural = errorCount === 1 ? 'field' : 'fields';
            sum.textContent = `Please fix ${errorCount} highlighted ${plural} before sending.`;
        }
        
        return errorCount === 0;
    }
    
    // Next / Back
    form.addEventListener('click', function(e){
        const next = e.target.closest('.next');
        const prev = e.target.closest('.prev');
        if (next){
            const i = visibleIndex();
            if (validateStep(i)) showStep(Math.min(steps.length-1, i+1));
        }
        if (prev){
            const i = visibleIndex();
            showStep(Math.max(0, i-1));
            announce('Returned to previous step');
        }
    });
    
    // Escape key handling - reset to first step
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape'){
            const currentStep = visibleIndex();
            if (currentStep > 0){
                // Ask for confirmation before resetting
                if (confirm('Do you want to go back to the first step?')){
                    showStep(0);
                    announce('Form reset to first step');
                }
            }
        }
    });
    
    // Submit to Formspree via AJAX (stay on page, show custom thank-you)
    form.addEventListener('submit', async function(e){
        e.preventDefault();
        
        // Set aria-busy on form
        form.setAttribute('aria-busy', 'true');
        announce('Submitting form, please wait...');
        
        // Ensure on final step & valid
        const i = visibleIndex();
        if (i !== steps.length-1){
            if (validateStep(steps.length-1)) showStep(steps.length-1);
            form.setAttribute('aria-busy', 'false');
            return;
        }
        if (!validateStep(i)) {
            form.setAttribute('aria-busy', 'false');
            return;
        }
        
        // Bot trap
        const hp = document.getElementById('_gotcha');
        if (hp && hp.value) { // silently succeed
            form.classList.add('est-hidden'); 
            thankyou.classList.remove('est-hidden');
            form.setAttribute('aria-busy', 'false');
            return;
        }
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn){ 
            submitBtn.disabled = true; 
            submitBtn.textContent = 'Sending…'; 
        }
        
        const fd = new FormData(form);
        try{
            const resp = await fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json' } // JSON = no redirect
            });
            if (resp.ok){
                form.classList.add('est-hidden');
                thankyou.classList.remove('est-hidden');
                const h = document.getElementById('thanks-heading');
                if (h) h.focus();
                form.reset();
                announce('Form submitted successfully. Thank you for your submission.');
            } else {
                // Try to read Formspree error messages
                let msg = 'There was a problem submitting your form. Please try again.';
                try{
                    const data = await resp.json();
                    if (data && data.errors && data.errors.length){
                        msg = data.errors.map(e => e.message).join(', ');
                    }
                }catch(_){}
                alert(msg);
                announce('Submission failed. ' + msg);
            }
        }catch(err){
            alert('Network error. Please check your connection and try again.');
            announce('Network error occurred. Please check your connection.');
        }finally{
            if (submitBtn){ 
                submitBtn.disabled = false; 
                submitBtn.textContent = 'Send'; 
            }
            form.setAttribute('aria-busy', 'false');
        }
    });
    
    // init
    showStep(0);
})();