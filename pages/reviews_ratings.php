<?php 
include '../includes/db.php'; 
include '../includes/header.php'; 

$mockReviews = [
    [
        'id' => 1,
        'name' => "John Dela Cruz",
        'rating' => 5,
        'comment' => "Jollibee never disappoints! The Chickenjoy is always crispy on the outside and juicy on the inside. Definitely my comfort food every weekend."
    ],

    [
        'id' => 2,
        'name' => "Maria Santos",
        'rating' => 5,
        'comment' => "The Jolly Spaghetti tastes so nostalgic and delicious. I also love the Peach Mango Pie because it’s always hot and crispy!"
    ],

    [
        'id' => 3,
        'name' => "Kevin Ramirez",
        'rating' => 4,
        'comment' => "Great food and fast service. The Champ Burger was surprisingly filling and tasty. Would order again with extra fries next time."
    ],

    [
        'id' => 4,
        'name' => "Angela Reyes",
        'rating' => 5,
        'comment' => "The Family Bundle was perfect for our family dinner. Affordable, delicious, and everyone enjoyed the Chickenjoy and spaghetti combo."
    ],

    [
        'id' => 5,
        'name' => "Mark Villanueva",
        'rating' => 4,
        'comment' => "I really enjoyed the Burger Steak meal. The gravy was flavorful and the serving size was worth the price."
    ],

    [
        'id' => 6,
        'name' => "Samantha Lopez",
        'rating' => 5,
        'comment' => "Their breakfast meals are amazing! The Longganisa Breakfast is my favorite, especially with garlic rice and egg."
    ]
];

$ratingDistribution = [
    ['stars' => 5, 'percentage' => 70],
    ['stars' => 4, 'percentage' => 50],
    ['stars' => 3, 'percentage' => 40],
    ['stars' => 2, 'percentage' => 20],
    ['stars' => 1, 'percentage' => 15]
];

$averageRating = 4.5;

function render_stars($rating) {
    $output = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= round($rating)) {
            $output .= '<span class="star-filled">★</span>';
        } else {
            $output .= '<span class="star-empty">☆</span>';
        }
    }
    return $output;
}
?>

<section class="review-section">
    <div class="container">
        <h1>Review and Ratings</h1>

        <div class="rating-overview-grid">
            <div class="average-rating-display">
                <div class="score"><?php echo number_format($averageRating, 1); ?></div>
                <div class="star-rating-lg">
                    <?php echo render_stars($averageRating); ?>
                </div>
                <p style="color: var(--text-light); font-weight: 500;">Average Rating</p>
            </div>

            <div class="rating-distribution">
                <?php foreach($ratingDistribution as $dist): ?>
                    <div class="rating-bar-row">
                        <span><?php echo $dist['stars']; ?>★</span>
                        <div class="rating-bar-container">
                            <div class="rating-bar-fill" style="width: <?php echo $dist['percentage']; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="reviews-list-container">
            <h2 style="font-size: 1.8rem; margin-bottom: 2rem; font-weight: 700;">Customer Reviews</h2>
            <div>
                <?php foreach($mockReviews as $review): ?>
                    <div class="review-card">
                        <div class="review-avatar">
                            <i class="material-icons">person</i>
                        </div>
                        <div class="review-content">
                            <h3><?php echo htmlspecialchars($review['name']); ?></h3>
                            <div class="review-star-row">
                                <?php echo render_stars($review['rating']); ?>
                            </div>
                            <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="review-form-grid">
            <div style="grid-column: 1 / -1;">
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem; font-weight: 700;">Leave Your Rating</h2>
            </div>
            <div>
                <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Comments</label>
                <textarea class="form-control" placeholder="Write your review..." disabled></textarea>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 6px;">*This form is non-functional in the current version.</p>
            </div>
            <div>
                <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Rating</label>
                <div class="review-rating-input">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" disabled style="background: none; border: none; cursor: default;">
                            <span class="review-star-lg star-filled">★</span>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>