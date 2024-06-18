import matplotlib.pyplot as plt
import json

# Load data from JSON file
with open('hourly_counts.json') as f:
    hourly_counts = json.load(f)

plt.figure(figsize=(14, 7))
for stop, hours in hourly_counts.items():
    hours_sorted = dict(sorted(hours.items()))
    plt.plot(hours_sorted.keys(), hours_sorted.values(), label=stop)

plt.title('Counts per Hour for Top 5 Stops in Year 2023')
plt.xlabel('Hour')
plt.ylabel('Count')
plt.legend(title="Top 5 Stops")
plt.grid(True)
plt.show()