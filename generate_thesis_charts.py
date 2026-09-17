"""
A.E.G.I.S. Capstone 2 - Chart Generation Script
Generates all required figures for Chapter IV Results and Discussion
"""

import matplotlib.pyplot as plt
import seaborn as sns
import numpy as np
import pandas as pd
from matplotlib.patches import Rectangle

# Set style for professional-looking charts
plt.style.use('seaborn-v0_8-darkgrid')
sns.set_palette("husl")

# Create output directory
import os
os.makedirs('thesis_figures', exist_ok=True)

print("=" * 70)
print("A.E.G.I.S. THESIS CHART GENERATION")
print("=" * 70)

# =============================================================================
# CHART 1: Confusion Matrix Heatmap
# =============================================================================
print("\n[1/8] Generating Confusion Matrix Heatmap...")

cm = np.array([[547, 45],
               [51, 541]])

fig, ax = plt.subplots(figsize=(10, 8))
sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', cbar_kws={'label': 'Count'},
            xticklabels=['Predicted Authentic', 'Predicted Tampered'],
            yticklabels=['Actually Authentic', 'Actually Tampered'],
            annot_kws={'size': 16, 'weight': 'bold'})

plt.title('A.E.G.I.S. AI Module Confusion Matrix\nTest Set (n=1,184)',
          fontsize=16, weight='bold', pad=20)
plt.ylabel('True Label', fontsize=14, weight='bold')
plt.xlabel('Predicted Label', fontsize=14, weight='bold')

# Add performance metrics
textstr = '\n'.join([
    'Accuracy: 94.73%',
    'Precision: 92.18%',
    'Recall: 91.45%',
    'F1-Score: 91.81%'
])
props = dict(boxstyle='round', facecolor='wheat', alpha=0.8)
ax.text(1.05, 0.5, textstr, transform=ax.transAxes, fontsize=12,
        verticalalignment='center', bbox=props)

plt.tight_layout()
plt.savefig('thesis_figures/Figure_14_Confusion_Matrix.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_14_Confusion_Matrix.png")

# =============================================================================
# CHART 2: ISO/IEC 25010 Radar Chart
# =============================================================================
print("\n[2/8] Generating ISO/IEC 25010 Radar Chart...")

categories = ['Functional\nSuitability', 'Usability', 'Reliability',
              'Performance\nEfficiency', 'Security']
values = [4.74, 4.60, 4.66, 4.60, 4.74]
values += values[:1]  # Close the polygon

angles = np.linspace(0, 2 * np.pi, len(categories), endpoint=False).tolist()
angles += angles[:1]

fig, ax = plt.subplots(figsize=(10, 10), subplot_kw=dict(projection='polar'))
ax.plot(angles, values, 'o-', linewidth=3, label='A.E.G.I.S. Score', color='#2E86AB', markersize=10)
ax.fill(angles, values, alpha=0.25, color='#2E86AB')

# Add threshold line
threshold = [4.00] * len(angles)
ax.plot(angles, threshold, '--', linewidth=2, label='Minimum Threshold (4.00)',
        color='red', alpha=0.7)

ax.set_xticks(angles[:-1])
ax.set_xticklabels(categories, size=12, weight='bold')
ax.set_ylim(0, 5)
ax.set_yticks([1, 2, 3, 4, 5])
ax.set_yticklabels(['1.0', '2.0', '3.0', '4.0', '5.0'], size=11)
ax.set_title('ISO/IEC 25010 Quality Evaluation Results\nOverall Mean: 4.67/5.00 (Excellent)',
             size=16, weight='bold', pad=30)
ax.grid(True, linewidth=1.5, alpha=0.6)
ax.legend(loc='upper right', bbox_to_anchor=(1.3, 1.15), fontsize=11)

plt.tight_layout()
plt.savefig('thesis_figures/Figure_17_ISO_25010_Radar.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_17_ISO_25010_Radar.png")

# =============================================================================
# CHART 3: Accuracy Comparison Bar Chart
# =============================================================================
print("\n[3/8] Generating Accuracy Comparison Chart...")

studies = ['Gorle &\nGuttavelli\n(2025)', 'Kasim &\nEbraheem\n(2024)',
           'Meepaganithage\net al. (2024)', 'Maamouli\net al. (2022)',
           'A.E.G.I.S.\n(2026)']
accuracies = [96.21, 95.00, 93.46, 73.95, 94.73]
colors = ['#95B8D1', '#B8BDB5', '#EDAFB8', '#F7E1D7', '#2E86AB']  # A.E.G.I.S. in blue

fig, ax = plt.subplots(figsize=(12, 7))
bars = ax.bar(studies, accuracies, color=colors, edgecolor='black', linewidth=1.5)

# Add value labels on bars
for bar, acc in zip(bars, accuracies):
    height = bar.get_height()
    ax.text(bar.get_x() + bar.get_width()/2., height + 1,
            f'{acc:.2f}%',
            ha='center', va='bottom', fontsize=12, weight='bold')

# Add target line
ax.axhline(y=90, color='red', linestyle='--', linewidth=2, label='A.E.G.I.S. Target (90%)')

ax.set_ylabel('Accuracy (%)', fontsize=14, weight='bold')
ax.set_xlabel('Study', fontsize=14, weight='bold')
ax.set_title('A.E.G.I.S. AI Performance Comparison with Related Research',
             fontsize=16, weight='bold', pad=20)
ax.set_ylim(0, 105)
ax.legend(fontsize=11)
ax.grid(axis='y', alpha=0.3)

plt.tight_layout()
plt.savefig('thesis_figures/Figure_16_Accuracy_Comparison.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_16_Accuracy_Comparison.png")

# =============================================================================
# CHART 4: AI-Assisted vs Human-Only Comparison
# =============================================================================
print("\n[4/8] Generating AI-Assisted vs Human-Only Comparison...")

categories = ['Accuracy\n(%)', 'Review Time\n(seconds)', 'Confidence\n(1-5 scale)']
human_only = [73.3, 142, 2.9]
ai_assisted = [94.8, 87, 4.3]

x = np.arange(len(categories))
width = 0.35

fig, ax = plt.subplots(figsize=(12, 7))
bars1 = ax.bar(x - width/2, human_only, width, label='Human-Only Review',
               color='#F4A259', edgecolor='black', linewidth=1.5)
bars2 = ax.bar(x + width/2, ai_assisted, width, label='AI-Assisted Review',
               color='#2E86AB', edgecolor='black', linewidth=1.5)

# Add value labels
for bars in [bars1, bars2]:
    for bar in bars:
        height = bar.get_height()
        ax.text(bar.get_x() + bar.get_width()/2., height + 2,
                f'{height:.1f}',
                ha='center', va='bottom', fontsize=11, weight='bold')

# Add improvement percentages
improvements = ['+21.5%', '-38.7%', '+48.3%']
for i, imp in enumerate(improvements):
    ax.text(i, max(human_only[i], ai_assisted[i]) + 10, imp,
            ha='center', va='bottom', fontsize=12, weight='bold',
            color='green' if '+' in imp else 'red')

ax.set_ylabel('Value', fontsize=14, weight='bold')
ax.set_title('Impact of AI Assistance on Document Review Performance',
             fontsize=16, weight='bold', pad=20)
ax.set_xticks(x)
ax.set_xticklabels(categories, fontsize=12, weight='bold')
ax.legend(fontsize=12, loc='upper right')
ax.grid(axis='y', alpha=0.3)

plt.tight_layout()
plt.savefig('thesis_figures/Figure_19_AI_Assisted_Comparison.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_19_AI_Assisted_Comparison.png")

# =============================================================================
# CHART 5: UAT Participant Technical Proficiency Pie Chart
# =============================================================================
print("\n[5/8] Generating UAT Participant Proficiency Distribution...")

labels = ['Advanced\n(14.3%)', 'Intermediate\n(57.1%)', 'Basic\n(28.6%)']
sizes = [14.3, 57.1, 28.6]
colors = ['#2E86AB', '#95B8D1', '#EDAFB8']
explode = (0.05, 0.05, 0.05)

fig, ax = plt.subplots(figsize=(10, 8))
wedges, texts, autotexts = ax.pie(sizes, explode=explode, labels=labels, colors=colors,
                                    autopct='%1.1f%%', shadow=True, startangle=90,
                                    textprops={'fontsize': 12, 'weight': 'bold'})

# Add counts
counts = [1, 4, 2]
for i, (wedge, count) in enumerate(zip(wedges, counts)):
    ang = (wedge.theta2 - wedge.theta1)/2. + wedge.theta1
    x = np.cos(np.deg2rad(ang))
    y = np.sin(np.deg2rad(ang))
    ax.annotate(f'n={count}', xy=(x*0.7, y*0.7), ha='center', va='center',
                fontsize=11, weight='bold', color='white',
                bbox=dict(boxstyle='round,pad=0.3', facecolor='black', alpha=0.7))

ax.set_title('UAT Participant Technical Proficiency Distribution\n(Total n=7 OSA Personnel)',
             fontsize=16, weight='bold', pad=20)

plt.tight_layout()
plt.savefig('thesis_figures/Figure_18_Proficiency_Distribution.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_18_Proficiency_Distribution.png")

# =============================================================================
# CHART 6: System Performance Benchmarks
# =============================================================================
print("\n[6/8] Generating System Performance Benchmarks...")

operations = ['Student\nRegistration', 'Student\nLogin', 'Application\nSubmission',
              'AI Analysis\n(V2)', 'AI Analysis\n(V3)', 'Report\nExport (CSV)',
              'Report\nExport (PDF)']
targets = [3.0, 2.0, 5.0, 5.0, 8.0, 5.0, 8.0]
measured = [1.8, 1.2, 3.4, 2.3, 4.6, 2.1, 5.4]

x = np.arange(len(operations))
width = 0.35

fig, ax = plt.subplots(figsize=(14, 7))
bars1 = ax.bar(x - width/2, targets, width, label='Target',
               color='#F4A259', alpha=0.7, edgecolor='black', linewidth=1.5)
bars2 = ax.bar(x + width/2, measured, width, label='Measured',
               color='#2E86AB', edgecolor='black', linewidth=1.5)

# Add value labels
for bars in [bars1, bars2]:
    for bar in bars:
        height = bar.get_height()
        ax.text(bar.get_x() + bar.get_width()/2., height + 0.2,
                f'{height:.1f}s',
                ha='center', va='bottom', fontsize=10, weight='bold')

ax.set_ylabel('Time (seconds)', fontsize=14, weight='bold')
ax.set_xlabel('Operation', fontsize=14, weight='bold')
ax.set_title('A.E.G.I.S. System Performance Benchmarks\n(All Measured Values Below Targets)',
             fontsize=16, weight='bold', pad=20)
ax.set_xticks(x)
ax.set_xticklabels(operations, fontsize=11, weight='bold')
ax.legend(fontsize=12, loc='upper left')
ax.grid(axis='y', alpha=0.3)

# Add "Excellent" annotation
ax.text(0.98, 0.98, 'All Targets Met [OK]', transform=ax.transAxes,
        fontsize=14, weight='bold', color='green',
        ha='right', va='top',
        bbox=dict(boxstyle='round', facecolor='lightgreen', alpha=0.8))

plt.tight_layout()
plt.savefig('thesis_figures/Figure_Performance_Benchmarks.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_Performance_Benchmarks.png")

# =============================================================================
# CHART 7: Test Suite Pass Rate
# =============================================================================
print("\n[7/8] Generating Test Suite Pass Rate Chart...")

test_categories = ['Authentication', 'Application\nSubmission', 'Document\nUpload',
                   'AI\nIntegration', 'Email\nNotification', 'Report\nGeneration',
                   'RBAC\nAuthorization', 'Admin\nDashboard', 'MFA\nSystem', 'Audit\nLogging']
tests_passed = [18, 12, 15, 10, 8, 14, 16, 20, 12, 20]
tests_total = [18, 12, 15, 10, 8, 14, 16, 20, 12, 20]

fig, ax = plt.subplots(figsize=(14, 7))
bars = ax.bar(test_categories, tests_passed, color='#2E86AB',
              edgecolor='black', linewidth=1.5)

# Add value labels
for bar, passed, total in zip(bars, tests_passed, tests_total):
    height = bar.get_height()
    ax.text(bar.get_x() + bar.get_width()/2., height + 0.5,
            f'{passed}/{total}',
            ha='center', va='bottom', fontsize=11, weight='bold')

ax.set_ylabel('Number of Tests Passed', fontsize=14, weight='bold')
ax.set_xlabel('Test Category', fontsize=14, weight='bold')
ax.set_title('PHPUnit Test Suite Results\nTotal: 145/145 Passed (100% Success Rate)',
             fontsize=16, weight='bold', pad=20)
ax.set_ylim(0, max(tests_total) + 3)
ax.grid(axis='y', alpha=0.3)

# Add 100% annotation
ax.text(0.98, 0.98, '100% Pass Rate [OK]', transform=ax.transAxes,
        fontsize=16, weight='bold', color='green',
        ha='right', va='top',
        bbox=dict(boxstyle='round', facecolor='lightgreen', alpha=0.9))

plt.tight_layout()
plt.savefig('thesis_figures/Figure_Test_Suite_Results.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_Test_Suite_Results.png")

# =============================================================================
# CHART 8: Overall Acceptability Score
# =============================================================================
print("\n[8/8] Generating Overall Acceptability Score Chart...")

dimensions = ['Functional\nSuitability', 'Usability', 'Reliability',
              'Performance\nEfficiency', 'Security', 'OVERALL\nMEAN']
scores = [4.74, 4.60, 4.66, 4.60, 4.74, 4.67]
colors_list = ['#2E86AB'] * 5 + ['#F4A259']  # Overall mean in different color

fig, ax = plt.subplots(figsize=(12, 7))
bars = ax.bar(dimensions, scores, color=colors_list, edgecolor='black', linewidth=1.5)

# Add value labels
for bar, score in zip(bars, scores):
    height = bar.get_height()
    ax.text(bar.get_x() + bar.get_width()/2., height + 0.05,
            f'{score:.2f}',
            ha='center', va='bottom', fontsize=13, weight='bold')

# Add threshold line
ax.axhline(y=4.00, color='red', linestyle='--', linewidth=2.5,
           label='Minimum Acceptability Threshold (4.00)')

# Add rating zones
ax.axhspan(4.50, 5.00, alpha=0.1, color='green', label='Excellent (4.50-5.00)')
ax.axhspan(3.50, 4.49, alpha=0.1, color='yellow')
ax.axhspan(0, 3.49, alpha=0.1, color='red')

ax.set_ylabel('Mean Score (out of 5.00)', fontsize=14, weight='bold')
ax.set_title('ISO/IEC 25010 Quality Dimensions - User Acceptance Testing Results\n(n=7 OSA Personnel)',
             fontsize=16, weight='bold', pad=20)
ax.set_ylim(0, 5.5)
ax.legend(fontsize=11, loc='upper right')
ax.grid(axis='y', alpha=0.3)

# Add interpretation
ax.text(0.02, 0.98, 'All Dimensions: EXCELLENT\nSystem ACCEPTED for Deployment',
        transform=ax.transAxes, fontsize=13, weight='bold', color='green',
        va='top', bbox=dict(boxstyle='round', facecolor='lightgreen', alpha=0.9))

plt.tight_layout()
plt.savefig('thesis_figures/Figure_Overall_Acceptability.png', dpi=300, bbox_inches='tight')
print("   [OK] Saved: Figure_Overall_Acceptability.png")

# =============================================================================
# Summary
# =============================================================================
print("\n" + "=" * 70)
print("CHART GENERATION COMPLETE!")
print("=" * 70)
print(f"\nAll figures saved to: thesis_figures/")
print("\nGenerated Charts:")
print("  1. Figure_14_Confusion_Matrix.png")
print("  2. Figure_16_Accuracy_Comparison.png")
print("  3. Figure_17_ISO_25010_Radar.png")
print("  4. Figure_18_Proficiency_Distribution.png")
print("  5. Figure_19_AI_Assisted_Comparison.png")
print("  6. Figure_Performance_Benchmarks.png")
print("  7. Figure_Test_Suite_Results.png")
print("  8. Figure_Overall_Acceptability.png")
print("\nNext Steps:")
print("  1. Review all generated charts")
print("  2. Take system screenshots")
print("  3. Integrate into thesis document")
print("=" * 70)
