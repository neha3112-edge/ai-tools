<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Ensure $pdo is available (requires db.php to be loaded by caller or explicitly here)
global $pdo;

// Fetch unique available courses for the dropdown purely server-side
$comp_courses = [];
if ($pdo) {
    $c_stmt = $pdo->query("SELECT DISTINCT c.id, c.name, c.display_name, c.course_level 
        FROM courses c 
        JOIN university_courses uc ON c.id = uc.course_id 
        WHERE c.is_active = 1 AND uc.is_active = 1 
        ORDER BY c.name ASC");
    $comp_courses = $c_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Default options structure
// $lead_form_options = [
//    'form_id' => 'leadForm',           // Unique ID for the form and its wrappers
//    'heading' => 'Download Brochure', 
//    'subheading' => 'Academic Experts will assist you!',
//    'button_text' => 'Download Brochure',
//    'success_heading' => 'Thank You!',
//    'success_message' => 'Your request has been successfully submitted. Our academic experts will contact you soon.'
// ];

$opt = $lead_form_options ?? [];

$form_id = $opt['form_id'] ?? 'leadForm_' . uniqid();
$heading = $opt['heading'] ?? 'Request Information';
$subheading = $opt['subheading'] ?? 'Our academic experts will reach out to you shortly.';
$button_text = $opt['button_text'] ?? 'Submit';
$success_heading = $opt['success_heading'] ?? 'Thank You!';
$success_message = $opt['success_message'] ?? 'Your request has been successfully submitted.';
$lead_type = $opt['lead_type'] ?? 'brochure';
$form_name = $opt['form_name'] ?? $form_id; // Dynamic form name for CRM tracking

// Generate CSRF token if session exists
if (empty($_SESSION['lead_csrf_token'])) {
    $_SESSION['lead_csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['lead_csrf_token'];

?>
<div id="<?= e($form_id) ?>_Area">
    <?php if ($heading): ?>
        <h2 style="margin: 0; color: var(--accent-blue); text-align: center; font-size:1.3rem; padding-top:10px;">
            <?= e($heading) ?>
        </h2>
    <?php endif; ?>
    <?php if ($subheading): ?>
        <p
            style="text-align: center; color: var(--text-m); margin-bottom: 1.5rem; margin-top:0px; font-weight: 500; font-size: 0.85rem;">
            <?= e($subheading) ?>
        </p>
    <?php endif; ?>

    <form class="dynamic-lead-form" data-wrapper-id="<?= e($form_id) ?>" onsubmit="submitGenericLeadForm(event, this)">
        <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
        <input type="hidden" name="form_id_context" value="<?= e($form_id) ?>">
        <input type="hidden" name="lead_type" value="<?= e($lead_type) ?>">
        <input type="hidden" name="form_name" value="<?= e($form_name) ?>">
        <input type="hidden" class="dynamic-source-url" name="source_url" value="">
        <!-- UTM fields — filled by JS before submit -->
        <input type="hidden" class="utm-field" name="utm_source" value="">
        <input type="hidden" class="utm-field" name="utm_medium" value="">
        <input type="hidden" class="utm-field" name="utm_campaign" value="">
        <input type="hidden" class="utm-field" name="utm_term" value="">
        <input type="hidden" class="utm-field" name="utm_content" value="">

        <div class="brochure-form-group">
            <input type="text" name="name" placeholder="Enter Your Name" required>
        </div>
        <div class="brochure-form-group">
            <input type="email" name="email" placeholder="Enter Your Email" required>
        </div>
        <div class="brochure-form-group" style="display: flex;">
            <select name="country_code"
                style="width: 110px; margin-right: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: #f8fafc; font-size:0.85rem;"
                required>
                <option value="+91">🇮🇳 +91</option>
                <option value="+93">🇦🇫 +93</option>
                <option value="+355">🇦🇱 +355</option>
                <option value="+213">🇩🇿 +213</option>
                <option value="+376">🇦🇩 +376</option>
                <option value="+244">🇦🇴 +244</option>
                <option value="+1-268">🇦🇬 +1-268</option>
                <option value="+54">🇦🇷 +54</option>
                <option value="+374">🇦🇲 +374</option>
                <option value="+61">🇦🇺 +61</option>
                <option value="+43">🇦🇹 +43</option>
                <option value="+994">🇦🇿 +994</option>
                <option value="+1-242">🇧🇸 +1-242</option>
                <option value="+973">🇧🇭 +973</option>
                <option value="+880">🇧🇩 +880</option>
                <option value="+1-246">🇧🇧 +1-246</option>
                <option value="+375">🇧🇾 +375</option>
                <option value="+32">🇧🇪 +32</option>
                <option value="+501">🇧🇿 +501</option>
                <option value="+229">🇧🇯 +229</option>
                <option value="+975">🇧🇹 +975</option>
                <option value="+591">🇧🇴 +591</option>
                <option value="+387">🇧🇦 +387</option>
                <option value="+267">🇧🇼 +267</option>
                <option value="+55">🇧🇷 +55</option>
                <option value="+673">🇧🇳 +673</option>
                <option value="+359">🇧🇬 +359</option>
                <option value="+226">🇧🇫 +226</option>
                <option value="+257">🇧🇮 +257</option>
                <option value="+238">🇨🇻 +238</option>
                <option value="+855">🇰🇭 +855</option>
                <option value="+237">🇨🇲 +237</option>
                <option value="+1">🇨🇦 +1</option>
                <option value="+236">🇨🇫 +236</option>
                <option value="+235">🇹🇩 +235</option>
                <option value="+56">🇨🇱 +56</option>
                <option value="+86">🇨🇳 +86</option>
                <option value="+57">🇨🇴 +57</option>
                <option value="+269">🇰🇲 +269</option>
                <option value="+242">🇨🇬 +242</option>
                <option value="+243">🇨🇩 +243</option>
                <option value="+506">🇨🇷 +506</option>
                <option value="+225">🇨🇮 +225</option>
                <option value="+385">🇭🇷 +385</option>
                <option value="+53">🇨🇺 +53</option>
                <option value="+357">🇨🇾 +357</option>
                <option value="+420">🇨🇿 +420</option>
                <option value="+45">🇩🇰 +45</option>
                <option value="+253">🇩🇯 +253</option>
                <option value="+1-767">🇩🇲 +1-767</option>
                <option value="+1-809">🇩🇴 +1-809</option>
                <option value="+593">🇪🇨 +593</option>
                <option value="+20">🇪🇬 +20</option>
                <option value="+503">🇸🇻 +503</option>
                <option value="+240">🇬🇶 +240</option>
                <option value="+291">🇪🇷 +291</option>
                <option value="+372">🇪🇪 +372</option>
                <option value="+268">🇸🇿 +268</option>
                <option value="+251">🇪🇹 +251</option>
                <option value="+679">🇫🇯 +679</option>
                <option value="+358">🇫🇮 +358</option>
                <option value="+33">🇫🇷 +33</option>
                <option value="+241">🇬🇦 +241</option>
                <option value="+220">🇬🇲 +220</option>
                <option value="+995">🇬🇪 +995</option>
                <option value="+49">🇩🇪 +49</option>
                <option value="+233">🇬🇭 +233</option>
                <option value="+30">🇬🇷 +30</option>
                <option value="+1-473">🇬🇩 +1-473</option>
                <option value="+502">🇬🇹 +502</option>
                <option value="+224">🇬🇳 +224</option>
                <option value="+245">🇬🇼 +245</option>
                <option value="+592">🇬🇾 +592</option>
                <option value="+509">🇭🇹 +509</option>
                <option value="+504">🇭🇳 +504</option>
                <option value="+36">🇭🇺 +36</option>
                <option value="+354">🇮🇸 +354</option>
                <option value="+62">🇮🇩 +62</option>
                <option value="+98">🇮🇷 +98</option>
                <option value="+964">🇮🇶 +964</option>
                <option value="+353">🇮🇪 +353</option>
                <option value="+972">🇮🇱 +972</option>
                <option value="+39">🇮🇹 +39</option>
                <option value="+1-876">🇯🇲 +1-876</option>
                <option value="+81">🇯🇵 +81</option>
                <option value="+962">🇯🇴 +962</option>
                <option value="+7">🇰🇿 +7</option>
                <option value="+254">🇰🇪 +254</option>
                <option value="+686">🇰🇮 +686</option>
                <option value="+383">🇽🇰 +383</option>
                <option value="+965">🇰🇼 +965</option>
                <option value="+996">🇰🇬 +996</option>
                <option value="+856">🇱🇦 +856</option>
                <option value="+371">🇱🇻 +371</option>
                <option value="+961">🇱🇧 +961</option>
                <option value="+266">🇱🇸 +266</option>
                <option value="+231">🇱🇷 +231</option>
                <option value="+218">🇱🇾 +218</option>
                <option value="+423">🇱🇮 +423</option>
                <option value="+370">🇱🇹 +370</option>
                <option value="+352">🇱🇺 +352</option>
                <option value="+261">🇲🇬 +261</option>
                <option value="+265">🇲🇼 +265</option>
                <option value="+60">🇲🇾 +60</option>
                <option value="+960">🇲🇻 +960</option>
                <option value="+223">🇲🇱 +223</option>
                <option value="+356">🇲🇹 +356</option>
                <option value="+692">🇲🇭 +692</option>
                <option value="+222">🇲🇷 +222</option>
                <option value="+230">🇲🇺 +230</option>
                <option value="+52">🇲🇽 +52</option>
                <option value="+691">🇫🇲 +691</option>
                <option value="+373">🇲🇩 +373</option>
                <option value="+377">🇲🇨 +377</option>
                <option value="+976">🇲🇳 +976</option>
                <option value="+382">🇲🇪 +382</option>
                <option value="+212">🇲🇦 +212</option>
                <option value="+258">🇲🇿 +258</option>
                <option value="+95">🇲🇲 +95</option>
                <option value="+264">🇳🇦 +264</option>
                <option value="+674">🇳🇷 +674</option>
                <option value="+977">🇳🇵 +977</option>
                <option value="+31">🇳🇱 +31</option>
                <option value="+64">🇳🇿 +64</option>
                <option value="+505">🇳🇮 +505</option>
                <option value="+227">🇳🇪 +227</option>
                <option value="+234">🇳🇬 +234</option>
                <option value="+389">🇲🇰 +389</option>
                <option value="+47">🇳🇴 +47</option>
                <option value="+968">🇴🇲 +968</option>
                <option value="+92">🇵🇰 +92</option>
                <option value="+680">🇵🇼 +680</option>
                <option value="+507">🇵🇦 +507</option>
                <option value="+675">🇵🇬 +675</option>
                <option value="+595">🇵🇾 +595</option>
                <option value="+51">🇵🇪 +51</option>
                <option value="+63">🇵🇭 +63</option>
                <option value="+48">🇵🇱 +48</option>
                <option value="+351">🇵🇹 +351</option>
                <option value="+974">🇶🇦 +974</option>
                <option value="+40">🇷🇴 +40</option>
                <option value="+7">🇷🇺 +7</option>
                <option value="+250">🇷🇼 +250</option>
                <option value="+1-869">🇰🇳 +1-869</option>
                <option value="+1-758">🇱🇨 +1-758</option>
                <option value="+1-784">🇻🇨 +1-784</option>
                <option value="+685">🇼🇸 +685</option>
                <option value="+378">🇸🇲 +378</option>
                <option value="+239">🇸🇹 +239</option>
                <option value="+966">🇸🇦 +966</option>
                <option value="+221">🇸🇳 +221</option>
                <option value="+381">🇷🇸 +381</option>
                <option value="+248">🇸🇨 +248</option>
                <option value="+232">🇸🇱 +232</option>
                <option value="+65">🇸🇬 +65</option>
                <option value="+421">🇸🇰 +421</option>
                <option value="+386">🇸🇮 +386</option>
                <option value="+677">🇸🇧 +677</option>
                <option value="+252">🇸🇴 +252</option>
                <option value="+27">🇿🇦 +27</option>
                <option value="+211">🇸🇸 +211</option>
                <option value="+34">🇪🇸 +34</option>
                <option value="+94">🇱🇰 +94</option>
                <option value="+249">🇸🇩 +249</option>
                <option value="+597">🇸🇷 +597</option>
                <option value="+46">🇸🇪 +46</option>
                <option value="+41">🇨🇭 +41</option>
                <option value="+963">🇸🇾 +963</option>
                <option value="+886">🇹🇼 +886</option>
                <option value="+992">🇹🇯 +992</option>
                <option value="+255">🇹🇿 +255</option>
                <option value="+66">🇹🇭 +66</option>
                <option value="+670">🇹🇱 +670</option>
                <option value="+228">🇹🇬 +228</option>
                <option value="+676">🇹🇴 +676</option>
                <option value="+1-868">🇹🇹 +1-868</option>
                <option value="+216">🇹🇳 +216</option>
                <option value="+90">🇹🇷 +90</option>
                <option value="+993">🇹🇲 +993</option>
                <option value="+688">🇹🇻 +688</option>
                <option value="+256">🇺🇬 +256</option>
                <option value="+380">🇺🇦 +380</option>
                <option value="+971">🇦🇪 +971</option>
                <option value="+44">🇬🇧 +44</option>
                <option value="+1">🇺🇸 +1</option>
                <option value="+598">🇺🇾 +598</option>
                <option value="+998">🇺🇿 +998</option>
                <option value="+678">🇻🇺 +678</option>
                <option value="+379">🇻🇦 +379</option>
                <option value="+58">🇻🇪 +58</option>
                <option value="+84">🇻🇳 +84</option>
                <option value="+967">🇾🇪 +967</option>
                <option value="+260">🇿🇲 +260</option>
                <option value="+263">🇿🇼 +263</option>
            </select>
            <input type="tel" name="phone" placeholder="Enter your Number" required pattern="[0-9]{8,15}"
                title="8 to 15 digit mobile number" style="flex:1;">
        </div>
        <div class="brochure-form-group">
            <select name="course" required>
                <option value="" disabled selected>Select Your Course</option>
                <option value="BA">BA</option>
                <option value="BBA">BBA</option>
                <option value="BCA">BCA</option>
                <option value="BSC">BSC</option>
                <option value="BCOM">BCOM</option>
                <option value="BTECH">BTECH</option>
                <option value="BLIS">BLIS</option>
                <option value="DIPLOMA">DIPLOMA</option>
                <option value="MA">MA</option>
                <option value="MCOM">MCOM</option>
                <option value="MSC">MSC</option>
                <option value="MBA">MBA</option>
                <option value="MCA">MCA</option>
                <option value="MLIS">MLIS</option>
                <option value="MTECH">MTECH</option>
                <option value="MSW">MSW</option>
                <option value="PG DIPLOMA">PG DIPLOMA</option>
            </select>
        </div>
        <div class="brochure-form-group">
            <select name="state" required>
                <option value="" disabled selected>Select Your State</option>
                <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                <option value="Andhra Pradesh">Andhra Pradesh</option>
                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                <option value="Assam">Assam</option>
                <option value="Bihar">Bihar</option>
                <option value="Chandigarh">Chandigarh</option>
                <option value="Chhattisgarh">Chhattisgarh</option>
                <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                <option value="Delhi">Delhi</option>
                <option value="Goa">Goa</option>
                <option value="Gujarat">Gujarat</option>
                <option value="Haryana">Haryana</option>
                <option value="Himachal Pradesh">Himachal Pradesh</option>
                <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                <option value="Jharkhand">Jharkhand</option>
                <option value="Karnataka">Karnataka</option>
                <option value="Kerala">Kerala</option>
                <option value="Ladakh">Ladakh</option>
                <option value="Lakshadweep">Lakshadweep</option>
                <option value="Madhya Pradesh">Madhya Pradesh</option>
                <option value="Maharashtra">Maharashtra</option>
                <option value="Manipur">Manipur</option>
                <option value="Meghalaya">Meghalaya</option>
                <option value="Mizoram">Mizoram</option>
                <option value="Nagaland">Nagaland</option>
                <option value="Odisha">Odisha</option>
                <option value="Puducherry">Puducherry</option>
                <option value="Punjab">Punjab</option>
                <option value="Rajasthan">Rajasthan</option>
                <option value="Sikkim">Sikkim</option>
                <option value="Tamil Nadu">Tamil Nadu</option>
                <option value="Telangana">Telangana</option>
                <option value="Tripura">Tripura</option>
                <option value="Uttar Pradesh">Uttar Pradesh</option>
                <option value="Uttarakhand">Uttarakhand</option>
                <option value="West Bengal">West Bengal</option>
            </select>
        </div>

        <div
            style="display: flex; align-items: flex-start; gap: 8px; margin-bottom: 1.5rem; font-size: 0.8rem; color: var(--text);">
            <input type="checkbox" name="consent" id="consent_<?= e($form_id) ?>" required style="margin-top: 3px;">
            <label for="consent_<?= e($form_id) ?>">I consent to receive university updates via email and mobile number.
                <!-- <a href="#" style="color:var(--accent-blue);">Disclaimer</a>--></label>
        </div>

        <button type="submit" class="btn btn-success lead-submit-btn"
            style="width: 100%; text-align:center; border-radius: var(--radius-sm); font-size: 1rem; font-weight: 600; padding: 6px 20px; background: #10b981;display: flex;align-items: center;justify-content: center;">
            <?= e($button_text) ?>
        </button>
    </form>
</div>

<!-- Success Message Element (Hidden by Default) -->
<div id="<?= e($form_id) ?>_Success" style="display:none; text-align:center; padding: 2rem 0;">
    <div
        style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>
    <h2 style="color: var(--accent-blue); margin-bottom: 0.5rem; font-size: 1.5rem;"><?= e($success_heading) ?></h2>
    <p style="color: var(--text-m); margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.5;">
        <?= e($success_message) ?>
    </p>
    <button type="button" class="btn btn-secondary trigger-modal-close" data-form-id="<?= e($form_id) ?>"
        style="width: fit-content; border-radius: 30px; font-weight: 600; padding: 10px 30px;    cursor: pointer;background: #10b981;color: #fff;">Close</button>
</div>