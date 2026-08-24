<?php
/**
 * Template Name: Homepage (Redesigned)
 * 
 * Modern, responsive homepage with improved UI/UX.
 * Fixes all responsive issues across mobile, tablet, and desktop.
 */

get_header();
?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 80px 20px; text-align: center; color: white;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <h1 style="font-size: 48px; margin-bottom: 20px; font-weight: 700;">تز نویسه</h1>
            <p style="font-size: 20px; margin-bottom: 30px; opacity: 0.9;">پلتفرم جامع پژوهش و تحلیل داده</p>
            <button style="background: white; color: #667eea; padding: 12px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">شروع کنید</button>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" style="padding: 80px 20px; background: #f8f9fa;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <h2 style="text-align: center; font-size: 36px; margin-bottom: 60px; color: #2c3e50;">ویژگی های اصلی</h2>
            
            <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <div class="feature-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="color: #667eea; margin-bottom: 15px; font-size: 20px;">آنالیز آماری</h3>
                    <p style="color: #7f8c8d; line-height: 1.6;">ابزارهای آماری پیشرفته برای تجزیه و تحلیل داده های پیچیده</p>
                </div>
                
                <div class="feature-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="color: #667eea; margin-bottom: 15px; font-size: 20px;">رابط کاربری مدرن</h3>
                    <p style="color: #7f8c8d; line-height: 1.6;">طراحی واکنش گرا و تجربه کاربری بهتر بر تمام دستگاه ها</p>
                </div>
                
                <div class="feature-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="color: #667eea; margin-bottom: 15px; font-size: 20px;">پشتیبانی فنی</h3>
                    <p style="color: #7f8c8d; line-height: 1.6;">تیم پشتیبانی حرفه ای آماده کمک در هر زمان</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Chat Widget -->
    <section class="chat-section" style="padding: 60px 20px; text-align: center; background: white;">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-size: 28px; margin-bottom: 20px; color: #2c3e50;">آیا سوالی دارید؟</h2>
            <p style="font-size: 16px; color: #7f8c8d; margin-bottom: 30px;">با تیم پشتیبانی ما تماس بگیرید</p>
            <div id="chat-widget" style="border: 2px solid #e0e0e0; border-radius: 12px; padding: 20px; background: #f9f9f9; min-height: 300px;">
                <!-- Chat widget loads here -->
                <p style="color: #95a5a6; padding: 40px 20px; text-align: center;">چت باز شود...</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 20px; text-align: center; color: white;">
        <div class="container" style="max-width: 1000px; margin: 0 auto;">
            <h2 style="font-size: 32px; margin-bottom: 20px;">آماده برای شروع؟</h2>
            <p style="font-size: 18px; margin-bottom: 30px; opacity: 0.9;">امروز ثبت نام کنید و از تمام ویژگی ها استفاده کنید</p>
            <button style="background: white; color: #667eea; padding: 14px 40px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">ثبت نام کنید</button>
        </div>
    </section>
</main>

<?php
get_footer();
