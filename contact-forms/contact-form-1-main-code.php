<?php
/**
 * Contact Form 1 Main Code Template
 * Theme-based contact form HTML that can be updated via theme updates
 * 
 * Dynamic elements:
 * - Form endpoint: Pulled from database or universal setting
 * - Services list: Dynamically generated from servicepage archetype posts
 * - Site identification: Added as hidden fields
 */

// Get form endpoint from database
global $wpdb;
$site_data = $wpdb->get_row("SELECT contact_form_1_endpoint FROM {$wpdb->prefix}zen_sitespren LIMIT 1");
$endpoint = $site_data ? esc_url($site_data->contact_form_1_endpoint) : '#';

// Always include site identification parameters
$site_name = get_bloginfo('name');
$site_url = home_url();
$site_domain = $_SERVER['HTTP_HOST'] ?? '';
?>

<section class="est-wrap" id="estimate-widget">
    <!-- live region for screen readers -->
    <div class="sr-only" aria-live="polite" aria-atomic="true" id="est-aria-live"></div>
    
    <form id="estimateForm" action="<?php echo $endpoint; ?>" method="POST" novalidate>
        <!-- Site identification (placed first to appear at top of email) -->
        <input type="hidden" name="site_name" value="<?php echo esc_attr($site_name); ?>">
        <input type="hidden" name="site_url" value="<?php echo esc_url($site_url); ?>">
        <input type="hidden" name="site_domain" value="<?php echo esc_attr($site_domain); ?>">
        
        <div class="est-card" role="group" aria-labelledby="est-title">
            <h2 class="est-title" id="est-title">Get A Free Estimate Today!</h2>
            
            <!-- ================== STEP 1 ======================== -->
            <div class="form-step" data-step="1" aria-labelledby="step1-heading">
                <strong class="est-subhead" id="step1-heading" tabindex="-1">Get a Free Estimate Now</strong>
                <hr class="est-rule">
                
                <label class="est-block" for="service_needed">
                    <span class="est-label">Service needed: <span aria-label="required" style="color: #d93025;">*</span></span>
                    <div id="service-help" class="est-help">Choose the closest match.</div>
                    <select id="service_needed" name="service_needed" required aria-describedby="service-help">
                        <option value="" selected disabled>Select</option>
                        <?php
                        // Dynamic service list generation
                        echo do_shortcode('[ruplin_shortcode_to_list_services_for_contact_form]');
                        ?>
                    </select>
                </label>
                
                <fieldset class="est-fs" aria-describedby="status-help">
                    <legend>Choose the appropriate status for this project:</legend>
                    <div id="status-help" class="est-help">This helps prioritize your request.</div>
                    <div class="est-chip-row" role="radiogroup" aria-label="Project status">
                        <label class="est-chip">
                            <input type="radio" name="project_status" value="Ready to Hire" required>
                            <span>Ready to Hire</span>
                        </label>
                        <label class="est-chip">
                            <input type="radio" name="project_status" value="Planning & Budgeting">
                            <span>Planning &amp; Budgeting</span>
                        </label>
                    </div>
                </fieldset>
                
                <fieldset class="est-fs" aria-describedby="timeline-help">
                    <legend>What is the timeline for this project?</legend>
                    <div id="timeline-help" class="est-help">An estimate window is fine.</div>
                    <div class="est-chip-grid" role="radiogroup" aria-label="Project timeline">
                        <label class="est-chip">
                            <input type="radio" name="timeline" value="Timing is Flexible" required>
                            <span>Timing is Flexible</span>
                        </label>
                        <label class="est-chip">
                            <input type="radio" name="timeline" value="Within 1 week">
                            <span>Within 1 week</span>
                        </label>
                        <label class="est-chip">
                            <input type="radio" name="timeline" value="1 - 2 Weeks">
                            <span>1 - 2 Weeks</span>
                        </label>
                        <label class="est-chip">
                            <input type="radio" name="timeline" value="More than 2 Weeks">
                            <span>More than 2 Weeks</span>
                        </label>
                    </div>
                </fieldset>
                
                <fieldset class="est-fs">
                    <legend>Are you currently the owner of this home?</legend>
                    <div class="est-chip-col" role="radiogroup" aria-label="Property owner">
                        <label class="est-chip">
                            <input type="radio" name="property_owner" value="Yes" required>
                            <span>Yes</span>
                        </label>
                        <label class="est-chip">
                            <input type="radio" name="property_owner" value="No, but I am authorized to make improvements">
                            <span>No, but I am authorized to make improvements</span>
                        </label>
                        <label class="est-chip">
                            <input type="radio" name="property_owner" value="No">
                            <span>No</span>
                        </label>
                    </div>
                </fieldset>
                
                <div class="est-btn-row">
                    <button type="button" class="est-btn est-btn-submit next" id="to-step-2" aria-controls="step2-heading">Send</button>
                </div>
            </div>
            
            <!-- ================== STEP 2 ======================== -->
            <div class="form-step est-hidden" data-step="2" aria-hidden="true" aria-labelledby="step2-heading">
                <strong class="est-subhead" id="step2-heading" tabindex="-1">Contact Information</strong>
                <hr class="est-rule">
                
                <div class="est-grid-2">
                    <label class="est-block" for="first_name">
                        <span class="est-label">First Name <span aria-label="required" style="color: #d93025;">*</span></span>
                        <input id="first_name" type="text" name="first_name" required autocomplete="given-name" aria-required="true">
                    </label>
                    <label class="est-block" for="last_name">
                        <span class="est-label">Last Name <span aria-label="required" style="color: #d93025;">*</span></span>
                        <input id="last_name" type="text" name="last_name" required autocomplete="family-name" aria-required="true">
                    </label>
                </div>
                
                <div class="est-grid-2">
                    <label class="est-block" for="phone">
                        <span class="est-label">Phone Number <span aria-label="required" style="color: #d93025;">*</span></span>
                        <input id="phone" type="tel" name="phone" required autocomplete="tel" inputmode="tel" aria-required="true">
                    </label>
                    <label class="est-block" for="email">
                        <span class="est-label">Email <span aria-label="required" style="color: #d93025;">*</span></span>
                        <input id="email" type="email" name="email" required autocomplete="email" aria-required="true">
                    </label>
                </div>
                
                <div class="est-grid-2">
                    <label class="est-block" for="street">
                        <span class="est-label">Street Name <span aria-label="required" style="color: #d93025;">*</span></span>
                        <input id="street" type="text" name="street" placeholder="100 Main St" required autocomplete="address-line1">
                    </label>
                    <label class="est-block" for="zip">
                        <span class="est-label">Zip Code <span aria-label="required" style="color: #d93025;">*</span></span>
                        <input id="zip" type="text" name="zip" required inputmode="numeric" pattern="[0-9]{5}">
                    </label>
                </div>
                
                <label class="est-block" for="message">
                    <span class="est-label">Message</span>
                    <textarea id="message" name="message" rows="5"></textarea>
                </label>
                
                <!-- Metadata -->
                <input type="hidden" name="_subject" value="New Free Estimate Request">
                <input type="hidden" name="page_url" value="">
                
                <!-- Honeypot -->
                <label class="sr-only" for="_gotcha">Leave this field blank</label>
                <input id="_gotcha" type="text" name="_gotcha" class="est-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                
                <!-- Inline error summary -->
                <div class="est-error-summary" id="errorSummary" role="alert" aria-live="assertive" hidden></div>
                
                <div class="est-btn-row">
                    <button type="submit" class="est-btn est-btn-submit">Send</button>
                </div>
                
                <p class="est-disclaimer">
                    We respect your privacy and want to make sure you are aware that by submitting your contact information you
                    agree to be contacted about this request by phone, text, or email. Message and data rates may apply.
                </p>
            </div>
        </div>
    </form>
    
    <!-- ================== STEP 3 (Thank You Panel) ======================== -->
    <div id="thankyouPanel" class="est-hidden" aria-labelledby="thanks-heading">
        <div style="text-align:center; padding:12px 8px 8px;">
            <h2 class="est-title" style="margin-bottom:18px;">Get A Free Estimate Today!</h2>
            <!-- Green checkmark SVG -->
            <svg width="140" height="140" viewBox="0 0 140 140" style="display:block;margin:0 auto 20px;" aria-hidden="true">
                <!-- Outer circle with gradient -->
                <defs>
                    <linearGradient id="checkGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#5cb85c;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#4cae4c;stop-opacity:1" />
                    </linearGradient>
                    <filter id="checkShadow">
                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-opacity="0.2"/>
                    </filter>
                </defs>
                <!-- Circle background -->
                <circle cx="70" cy="70" r="65" fill="url(#checkGradient)" filter="url(#checkShadow)"/>
                <!-- White checkmark -->
                <path d="M45 70 L60 85 L95 50" 
                      stroke="white" 
                      stroke-width="7" 
                      fill="none" 
                      stroke-linecap="round" 
                      stroke-linejoin="round"
                      style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2))"/>
            </svg>
            <h2 id="thanks-heading" style="font-size:36px; margin:8px 0 8px; font-weight:800; color:#111;" tabindex="-1">
                Thank you!
            </h2>
            <p style="font-size:22px; line-height:1.35; font-weight:700; color:#111; max-width:720px; margin:0 auto 14px;">
                A contractor from our trusted network has been notified of your request and will be contacting you shortly.
            </p>
            <p style="opacity:.7; max-width:720px; margin:10px auto 6px;">
                If you are not available by phone they will contact you by email.
            </p>
        </div>
    </div>
</section>