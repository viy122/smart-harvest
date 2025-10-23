<?php
header("Content-Type: application/json");

// (Optional) Include config if you plan to use database connection later.
// require_once "../config.php";

// ✅ Disease information dataset
$DISEASE_RECOMMENDATIONS = [

    "Apple Scab Leaf" => [
        "treatments" => [
            "Apply fungicide containing captan, mancozeb, or sulfur every 7–10 days during the growing season.",
            "Remove and destroy fallen leaves and infected fruits to reduce spore spread.",
            "Prune overcrowded branches to improve air circulation."
        ],
        "prevention" => [
            "Choose scab-resistant apple varieties.",
            "Avoid overhead irrigation to reduce leaf wetness.",
            "Apply preventive fungicides early in the spring."
        ]
    ],

    "Apple leaf" => [
        "treatments" => [
            "Use a balanced fertilizer to improve overall plant health.",
            "Remove damaged or yellowing leaves to prevent fungal infection.",
            "Spray with neem oil to deter pests."
        ],
        "prevention" => [
            "Ensure proper spacing between trees for airflow.",
            "Water at the base of the tree, not on the leaves.",
            "Regularly inspect for signs of insects or fungal growth."
        ]
    ],

    "Apple rust leaf" => [
        "treatments" => [
            "Apply fungicides containing myclobutanil or propiconazole at early leaf development.",
            "Remove nearby juniper plants that may host rust spores.",
            "Prune and destroy affected leaves and branches."
        ],
        "prevention" => [
            "Avoid planting apple trees near cedar or juniper species.",
            "Use resistant cultivars when possible.",
            "Apply fungicide sprays during bud break and early fruit development."
        ]
    ],

    "Bell_pepper leaf" => [
        "treatments" => [
            "Provide consistent watering and avoid overwatering.",
            "Use insecticidal soap to remove aphids and whiteflies.",
            "Apply balanced fertilizer to restore nutrient levels."
        ],
        "prevention" => [
            "Plant in well-drained soil.",
            "Rotate crops every season to avoid soil-borne disease.",
            "Maintain proper plant spacing for airflow."
        ]
    ],

    "Bell_pepper leaf spot" => [
        "treatments" => [
            "Apply copper-based fungicides to reduce bacterial spread.",
            "Remove infected leaves immediately.",
            "Avoid handling wet plants to reduce bacteria transmission."
        ],
        "prevention" => [
            "Plant disease-free seeds or seedlings.",
            "Water at the soil level, not on leaves.",
            "Practice crop rotation every 2–3 years."
        ]
    ],

    "Blueberry leaf" => [
        "treatments" => [
            "Apply fungicides containing captan or chlorothalonil if fungal infection appears.",
            "Ensure soil pH is between 4.5–5.5 for healthy growth.",
            "Remove diseased or discolored leaves."
        ],
        "prevention" => [
            "Maintain proper soil acidity.",
            "Avoid wetting foliage during irrigation.",
            "Prune regularly to improve airflow."
        ]
    ],

    "Cherry leaf" => [
        "treatments" => [
            "Apply fungicide sprays like captan or copper hydroxide early in the season.",
            "Rake and destroy fallen leaves to reduce fungal spores.",
            "Prune infected twigs and branches."
        ],
        "prevention" => [
            "Plant resistant cherry cultivars.",
            "Avoid overhead watering.",
            "Use mulch to prevent soil splash onto leaves."
        ]
    ],

    "Corn Gray leaf spot" => [
        "treatments" => [
            "Apply fungicides containing strobilurins or triazoles when lesions first appear.",
            "Rotate crops to non-host species for 1–2 years.",
            "Destroy infected crop residue after harvest."
        ],
        "prevention" => [
            "Use resistant corn hybrids.",
            "Avoid planting in continuous corn fields.",
            "Maintain good field drainage."
        ]
    ],

    "Corn leaf blight" => [
        "treatments" => [
            "Use fungicide sprays like azoxystrobin or pyraclostrobin.",
            "Plow under old crop residue to reduce spores.",
            "Provide adequate spacing to enhance airflow."
        ],
        "prevention" => [
            "Rotate corn with non-host crops like soybeans or wheat.",
            "Plant blight-resistant hybrids.",
            "Monitor and control insect vectors."
        ]
    ],

    "Corn rust leaf" => [
        "treatments" => [
            "Apply fungicides such as propiconazole or pyraclostrobin when pustules appear.",
            "Remove severely infected leaves.",
            "Improve field sanitation by removing old plant debris."
        ],
        "prevention" => [
            "Select rust-resistant hybrids.",
            "Avoid dense planting that traps humidity.",
            "Monitor fields regularly during warm, humid weather."
        ]
    ],

    "Peach leaf" => [
        "treatments" => [
            "Apply a dormant spray with copper-based fungicide before bud swell.",
            "Remove and destroy infected leaves showing curl or spots.",
            "Fertilize appropriately to support new healthy growth."
        ],
        "prevention" => [
            "Use disease-resistant peach varieties.",
            "Avoid excessive nitrogen fertilization.",
            "Ensure proper pruning for sunlight and airflow."
        ]
    ],

    "Potato leaf" => [
        "treatments" => [
            "Inspect and remove damaged or yellowing leaves.",
            "Use neem oil or insecticidal soap to manage pests like aphids.",
            "Maintain balanced soil nutrients."
        ],
        "prevention" => [
            "Rotate potato crops every 2–3 years.",
            "Avoid overhead irrigation.",
            "Plant certified disease-free seed potatoes."
        ]
    ],

    "Potato leaf early blight" => [
        "treatments" => [
            "Apply fungicides containing chlorothalonil or mancozeb weekly after emergence.",
            "Remove lower infected leaves to reduce spread.",
            "Ensure adequate potassium and nitrogen in soil."
        ],
        "prevention" => [
            "Rotate crops with non-solanaceous plants.",
            "Avoid planting potatoes near tomatoes.",
            "Use resistant potato varieties."
        ]
    ],

    "Potato leaf late blight" => [
        "treatments" => [
            "Spray fungicides containing metalaxyl or cymoxanil immediately after detection.",
            "Destroy infected plants to prevent spread.",
            "Avoid working in wet fields to reduce spore movement."
        ],
        "prevention" => [
            "Use certified disease-free seed potatoes.",
            "Ensure proper spacing for airflow.",
            "Monitor weather forecasts for blight-prone conditions."
        ]
    ],

    "Raspberry leaf" => [
        "treatments" => [
            "Remove infected canes at ground level after fruiting.",
            "Apply sulfur-based fungicides to control leaf spots.",
            "Prune to improve air circulation."
        ],
        "prevention" => [
            "Plant raspberries in sunny, well-drained areas.",
            "Avoid overhead watering.",
            "Sanitize pruning tools between uses."
        ]
    ],

    "Soyabean leaf" => [
        "treatments" => [
            "Use fungicides containing azoxystrobin or tebuconazole during early infection.",
            "Destroy crop residue after harvest.",
            "Apply balanced fertilizer to strengthen plant immunity."
        ],
        "prevention" => [
            "Plant resistant soybean varieties.",
            "Rotate crops with non-legume species.",
            "Monitor regularly for early signs of rust or blight."
        ]
    ],
];

// ✅ Retrieve requested disease
$disease = $_GET["disease"] ?? "";

// ✅ Output JSON response
if (isset($DISEASE_RECOMMENDATIONS[$disease])) {
    echo json_encode([
        "success" => true,
        "disease" => $disease,
        "treatments" => $DISEASE_RECOMMENDATIONS[$disease]["treatments"],
        "prevention" => $DISEASE_RECOMMENDATIONS[$disease]["prevention"]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Disease not found."]);
}
